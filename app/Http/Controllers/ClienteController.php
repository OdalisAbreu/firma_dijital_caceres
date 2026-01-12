<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\KycSend;
use App\Services\ClienteService;
use App\Services\KycUsuarioUnicoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ClienteController extends Controller
{
    protected ClienteService $clienteService;

    public function __construct(ClienteService $clienteService)
    {
        $this->middleware('auth');
        $this->clienteService = $clienteService;
    }

    /**
     * Display a listing of the clients.
     */
    public function index(Request $request)
    {
        // Preparar filtros desde la request
        $filters = [
            'page' => $request->get('page', 1),
            'page_size' => $request->get('page_size', 10),
        ];

        // Agregar filtros opcionales solo si están presentes
        $optionalFilters = [
            'cnomcliente',
            'crnc',
            'ccedula',
            'tipo_cliente',
            'estatus',
            'sucursal',
            'es_prospecto',
        ];

        foreach ($optionalFilters as $filter) {
            if ($request->has($filter) && $request->get($filter) !== null && $request->get($filter) !== '') {
                $filters[$filter] = $request->get($filter);
            }
        }

        // Consumir el servicio
        $result = $this->clienteService->getClientes($filters);

        if (!$result['success']) {
            return Inertia::render('Clientes/Index', [
                'clientes' => null,
                'total' => 0,
                'error' => $result['error'] ?? 'Error al obtener los clientes',
                'filters' => $request->only($optionalFilters),
            ]);
        }

        $data = $result['data'] ?? [];
        
        // Simular paginación para Inertia
        $clientes = [
            'data' => $data['data'] ?? [],
            'current_page' => $data['page'] ?? 1,
            'per_page' => $data['page_size'] ?? 10,
            'total' => $data['total'] ?? 0,
            'last_page' => $data['total_pages'] ?? 1,
        ];

        return Inertia::render('Clientes/Index', [
            'clientes' => $clientes,
            'total' => $data['total'] ?? 0,
            'filters' => $request->only($optionalFilters),
        ]);
    }

    public function createKycUsurioUnico(Request $request){

        //integrar el servicio de kyc usuario unico
        $kycUsuarioUnicoService = new KycUsuarioUnicoService();

        $employee = Employee::find($request->employee_id);
        if (!$employee) {
            return response()->json(['error' => 'Empleado no encontrado'], 404);
        }
        $data = [
            'title' => 'Formulario KYC',
            'description' => 'Documento para firma electrónica',
            'name_client' => $request->name_client,
            'email_client' => $request->email_client,
            'name_employee' => $employee->name,
            'email_employee' => $employee->email,
            'tipo_tercero' => $request->tipo_tercero,
            'sucursal' => $request->sucursal,
            'tipodeidentificacion' => $request->tipodeidentificacion,
            'numero_identificacion' => $request->numero_identificacion,
            'name' => $request->name,
            'lastname' => $request->lastname,
            'code_employee' => $employee->code_employee,
        ];

        $response = $kycUsuarioUnicoService->enviarFormularioKyc($data);
        if ($response['success']) {
            //guarda los datos en la tabla kyc_sends
            $kycSend = new KycSend();
            $kycSend->email = $request->email_client;
            $kycSend->shipping_status = true;
            $kycSend->kyc_status = $response['data']['status'];
            $kycSend->employee_id = $employee->id;
            $kycSend->tracking_code = $response['data']['tracking_code'];
            $kycSend->client_code = $response['data']['client_code'];
            $kycSend->save();
        }else{
            Log::error('Error al enviar el formulario KYC: ' . $response['error']);
            return response()->json(['error' => 'Error al enviar el formulario KYC'], 500);
        }
        //redireccionar al dashboard
        return redirect()->route('dashboard');
    }
}
