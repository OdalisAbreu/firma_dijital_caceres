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

        // Obtener el empleado principal para el formulario KYC
        $majorEmployee = Employee::where('major_employee', true)->first();
        
        // Obtener todos los empleados para el dropdown
        $employees = Employee::orderBy('major_employee', 'desc')->orderBy('name')->get();

        return Inertia::render('Clientes/Index', [
            'clientes' => $clientes,
            'total' => $data['total'] ?? 0,
            'filters' => $request->only($optionalFilters),
            'majorEmployee' => $majorEmployee,
            'employees' => $employees, // Pasar todos los empleados para el dropdown
        ]);
    }

    public function createKycUsurioUnico(Request $request)
    {
        // Validar campos obligatorios
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'name_client' => 'required|string|max:255',
            'lastname_client' => 'required|string|max:255',
            'email_client' => 'required|email|max:255',
            'tipo_tercero' => 'required|in:Tomador,Asegurado,Beneficiario,Afianzado,Proveedor,Empleado,Apoderado',
            'sucursal' => 'required|in:Principal,Romana,Punta Cana',
            'tipodeidentificacion' => 'required|string|max:255',
            'numero_identificacion' => 'required|string|max:255',
        ]);

        // Integrar el servicio de kyc usuario unico
        $kycUsuarioUnicoService = new KycUsuarioUnicoService();

        $employee = Employee::findOrFail($validated['employee_id']);

        // Preparar todos los datos para el servicio
        $data = [
            'title' => 'Formulario KYC - ' . $validated['name_client'] . ' ' . $validated['lastname_client'],
            'description' => 'Documento para firma electrónica',
            'name_client' => $validated['name_client'],
            'lastname_client' => $validated['lastname_client'],
            'email_client' => $validated['email_client'],
            'name_employee' => $employee->name,
            'email_employee' => $employee->email,
            'tipo_tercero' => $validated['tipo_tercero'],
            'sucursal' => $validated['sucursal'],
            'tipodeidentificacion' => $validated['tipodeidentificacion'],
            'numero_identificacion' => $validated['numero_identificacion'],
            'name' => $validated['name_client'],
            'lastname' => $validated['lastname_client'],
            'code_employee' => (string)auth()->id(), // ID del usuario autenticado
            // Campos opcionales del metadataList
            'fechadevencimiento' => $request->fechadevencimiento,
            'sexo' => $request->sexo,
            'fechanacimiento' => $request->fechanacimiento,
            'ciudaddenacimiento' => $request->ciudaddenacimiento,
            'provinciadenacimiento' => $request->provinciadenacimiento,
            'nacionalidad' => $request->nacionalidad,
            'profesion' => $request->profesion,
            'ocupacioncargo' => $request->ocupacioncargo,
            'empresa' => $request->empresa,
            'direcciondondelabora' => $request->direcciondondelabora,
            'ciudad' => $request->ciudad,
            'provincia' => $request->provincia,
            'telefono' => $request->telefono,
            'ciudadresidencia' => $request->ciudadresidencia,
            'provinviaresidencia' => $request->provinviaresidencia,
            'pais' => $request->pais,
            'telefonoresidencia' => $request->telefonoresidencia,
            'celular' => $request->celular,
            'direccionresidencia' => $request->direccionresidencia,
            'sector' => $request->sector,
            'direccionparaenviarproductos' => $request->direccionparaenviarproductos,
            'informacionactividadeconomica' => $request->informacionactividadeconomica,
            'informacionfinanciera' => $request->informacionfinanciera,
            'otrosingresos' => $request->otrosingresos,
            'actividadeconomicadeotrosingresos' => $request->actividadeconomicadeotrosingresos,
            'recursospublicos' => $request->recursospublicos,
            'respuestaafirmativauno' => $request->respuestaafirmativauno,
            'poderpublico' => $request->poderpublico,
            'personareconocida' => $request->personareconocida,
            'afirmativaderespuesta' => $request->afirmativaderespuesta,
            'solicituddeseguro' => $request->solicituddeseguro,
            'fecha' => $request->fecha ?? now()->format('d/m/Y'),
        ];

        $response = $kycUsuarioUnicoService->enviarFormularioKyc($data);
        
        if ($response['success'] && isset($response['data'])) {
            $apiResponse = $response['data'];
            
            // Guardar los datos en la tabla kyc_sends
            KycSend::create([
                'email' => $validated['email_client'],
                'name_client' => $validated['name_client'],
                'lastname_client' => $validated['lastname_client'],
                'shipping_status' => isset($apiResponse['status']) && $apiResponse['status'] === 'RECEIVED',
                'kyc_status' => $apiResponse['status'] ?? 'UNKNOWN',
                'status_firmante01' => 'WAITING',
                'status_firmante02' => 'WAITING',
                'employee_id' => $employee->id,
                'tracking_code' => $apiResponse['code'] ?? null,
                'client_code' => $validated['numero_identificacion'] ?? null,
            ]);

            return redirect()->route('clientes.index')->with('success', 'Formulario KYC enviado exitosamente. Código de seguimiento: ' . ($apiResponse['code'] ?? 'N/A'));
        }

        Log::error('Error al enviar el formulario KYC: ' . ($response['error'] ?? 'Error desconocido'));
        return back()->withErrors([
            'kyc_error' => $response['error'] ?? 'Error al enviar el formulario KYC',
        ])->withInput();
    }
}
