<?php

namespace App\Http\Controllers;

use App\Models\KycSend;
use App\Services\KycUsuarioUnicoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class DashboardController extends Controller
{
    protected KycUsuarioUnicoService $kycService;

    public function __construct(KycUsuarioUnicoService $kycService)
    {
        $this->middleware('auth');
        $this->kycService = $kycService;
    }

    public function index()
    {
        // Obtener todos los KYC con la relación del usuario
        $kycSends = KycSend::with('user')
            ->latest()
            ->get();

        // Calcular estadísticas
        // Considerar completados aquellos con status RESPONSED
        $totalCompletados = $kycSends->filter(function ($kyc) {
            $status = strtoupper($kyc->kyc_status ?? '');
            return $status === 'RESPONSED';
        })->count();
        
        // El resto son pendientes
        $totalPendientes = $kycSends->count() - $totalCompletados;

        // Total de KYC Respondidos por clientes (status_firmante01 = RESPONSED)
        $totalRespondidosClientes = $kycSends->filter(function ($kyc) {
            $status = strtoupper($kyc->status_firmante01 ?? '');
            return $status === 'RESPONSED';
        })->count();

        // Total de KYC Respondidos por colaborador (status_firmante02 = RESPONSED)
        $totalRespondidosColaborador = $kycSends->filter(function ($kyc) {
            $status = strtoupper($kyc->status_firmante02 ?? '');
            return $status === 'RESPONSED';
        })->count();

        return Inertia::render('Dashboard', [
            'kycSends' => $kycSends,
            'totalCompletados' => $totalCompletados,
            'totalPendientes' => $totalPendientes,
            'totalRespondidosClientes' => $totalRespondidosClientes,
            'totalRespondidosColaborador' => $totalRespondidosColaborador,
        ]);
    }

    /**
     * Actualiza los estados de los KYC que no están completados
     */
    public function actualizarEstados(Request $request)
    {
        // Obtener todos los KYC que no están en estado RESPONSED y tienen tracking_code
        $kycSends = KycSend::whereNotNull('tracking_code')
            ->where(function ($query) {
                $query->where('kyc_status', '!=', 'RESPONSED')
                    ->orWhereNull('kyc_status');
            })
            ->get();

        $actualizados = 0;
        $errores = 0;

        foreach ($kycSends as $kyc) {
            if (!$kyc->tracking_code) {
                continue;
            }

            // Consultar el estatus del KYC
            $result = $this->kycService->consultarEstatus($kyc->tracking_code);
Log::info('result: ' . json_encode($result));
            if ($result['success'] && isset($result['data'])) {
                $apiResponse = $result['data'];

                // Actualizar kyc_status
                $kyc->kyc_status = $apiResponse['status'] ?? $kyc->kyc_status;

                // Variables temporales para almacenar los valores del API
                $statusFirmante01FromApi = null;
                $statusFirmante02FromApi = null;

                // Obtener los valores del API desde recipientStatus
                if (isset($apiResponse['recipientStatus']) && is_array($apiResponse['recipientStatus'])) {
                    foreach ($apiResponse['recipientStatus'] as $recipient) {
                        if (isset($recipient['key']) && isset($recipient['status'])) {
                            if ($recipient['key'] === 'firmante01') {
                                $statusFirmante01FromApi = $recipient['status'];
                            } elseif ($recipient['key'] === 'firmante02') {
                                $statusFirmante02FromApi = $recipient['status'];
                            }
                        }
                    }
                }

                // Actualizar status_firmante02 (siempre toma el valor del API)
                if ($statusFirmante02FromApi !== null) {
                    $kyc->status_firmante02 = $statusFirmante02FromApi;
                }

                // Lógica para status_firmante01:
                // Si status_firmante02 = RECEIVED, entonces status_firmante01 = RESPONSED
                // Si kyc_status = RESPONSED, entonces status_firmante01 = RESPONSED
                // En caso contrario, tomar el valor del API
                Log::info('statusFirmante02FromApi: ' . $statusFirmante02FromApi);
                if ($statusFirmante02FromApi === 'RECEIVED') {
                    $kyc->status_firmante01 = 'RESPONSED';
                } 
                if ($kyc->kyc_status === 'RESPONSED') {
                    $kyc->status_firmante01 = 'RESPONSED';
                    $kyc->status_firmante02 = 'RESPONSED';
                } elseif ($statusFirmante01FromApi !== null) {
                    $kyc->status_firmante01 = $statusFirmante01FromApi;
                    $kyc->status_firmante02 = $statusFirmante02FromApi;
                }

                $kyc->save();
                $actualizados++;
            } else {
                $errores++;
            }
        }

        return redirect()->route('dashboard')->with('success', "Se actualizaron {$actualizados} registros KYC. Errores: {$errores}");
    }
}
