<?php

namespace App\Http\Controllers;

use App\Models\KycSend;
use App\Models\User;
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

    public function index(Request $request)
    {
        $colaboradorId = $request->get('colaborador_id');

        // Filtro base aplicado a las estadísticas y a la tabla
        $baseQuery = KycSend::query();
        if ($colaboradorId) {
            $baseQuery->where('user_id', $colaboradorId);
        }

        // Calcular estadísticas sobre el total de registros filtrados (no solo la página actual)
        $totalKyc = (clone $baseQuery)->count();

        // Considerar completados aquellos con status RESPONSED
        $totalCompletados = (clone $baseQuery)->whereRaw('UPPER(kyc_status) = ?', ['RESPONSED'])->count();

        // El resto son pendientes
        $totalPendientes = $totalKyc - $totalCompletados;

        // Total de KYC Respondidos por clientes (status_firmante01 = RESPONSED)
        $totalRespondidosClientes = (clone $baseQuery)->whereRaw('UPPER(status_firmante01) = ?', ['RESPONSED'])->count();

        // Total de KYC Respondidos por colaborador (status_firmante02 = RESPONSED)
        $totalRespondidosColaborador = (clone $baseQuery)->whereRaw('UPPER(status_firmante02) = ?', ['RESPONSED'])->count();

        // Paginación de la tabla
        $pageSize = (int) $request->get('page_size', 10);
        if (!in_array($pageSize, [10, 30, 50, 100], true)) {
            $pageSize = 10;
        }

        $kycSends = (clone $baseQuery)
            ->with('user')
            ->latest()
            ->paginate($pageSize)
            ->withQueryString();

        // Colaboradores activos para el dropdown del filtro
        $colaboradores = User::where('role', 'colaborador')
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Dashboard', [
            'kycSends' => $kycSends,
            'totalCompletados' => $totalCompletados,
            'totalPendientes' => $totalPendientes,
            'totalRespondidosClientes' => $totalRespondidosClientes,
            'totalRespondidosColaborador' => $totalRespondidosColaborador,
            'colaboradores' => $colaboradores,
            'filters' => [
                'page_size' => $pageSize,
                'colaborador_id' => $colaboradorId ? (int) $colaboradorId : null,
            ],
        ]);
    }

    /**
     * Actualiza los estados de los KYC que no están completados
     */
    public function actualizarEstados(Request $request)
    {
        // Obtener todos los KYC que no están en estado RESPONSED ni EXPIRED y tienen tracking_code
        $kycSends = KycSend::whereNotNull('tracking_code')
            ->where(function ($query) {
                $query->whereNotIn('kyc_status', ['RESPONSED', 'EXPIRED'])
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

    /**
     * Elimina un registro KYC (solo administradores)
     */
    public function destroy(KycSend $kycSend)
    {
        abort_unless(auth()->user()?->role === 'administrador', 403);

        $kycSend->delete();

        return redirect()->route('dashboard')->with('success', 'Registro KYC eliminado correctamente.');
    }
}
