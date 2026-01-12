<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\KycSend;
use App\Services\KycUsuarioUnicoService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KycSendController extends Controller
{
    protected KycUsuarioUnicoService $kycService;

    public function __construct(KycUsuarioUnicoService $kycService)
    {
        $this->middleware('auth');
        $this->kycService = $kycService;
    }

    /**
     * Show the form for creating a new KYC send.
     */
    public function create()
    {
        // Obtener empleados ordenados: primero el major_employee, luego los demás
        $majorEmployee = Employee::where('major_employee', true)->first();
        $otherEmployees = Employee::where('major_employee', false)->orWhereNull('major_employee')->get();
        
        $employees = collect();
        if ($majorEmployee) {
            $employees->push($majorEmployee);
        }
        $employees = $employees->merge($otherEmployees);

        return Inertia::render('KycSend/Create', [
            'employees' => $employees->values(),
        ]);
    }

    /**
     * Store a newly created KYC send.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'name_client' => 'required|string|max:255',
            'lastname_client' => 'required|string|max:255',
            'email_client' => 'required|email|max:255',
            'tipo_identificacion' => 'required|in:Cédula,Pasaporte,ID residencia',
            'numero_identificacion' => 'required|string|max:255',
            'tipo_tercero' => 'required|string|max:255',
            'sucursal' => 'required|in:Principal,Romana,Punta Cana',
        ]);

        // Obtener el empleado seleccionado
        $employee = Employee::findOrFail($validated['employee_id']);

        // Preparar datos para el servicio
        $kycData = [
            'title' => $validated['title'] ?? 'Formulario KYC - ' . $validated['name_client'] . ' ' . $validated['lastname_client'],
            'description' => $validated['description'] ?? 'Documento para firma electrónica',
            'name_client' => $validated['name_client'],
            'email_client' => $validated['email_client'],
            'name_employee' => $employee->name,
            'email_employee' => $employee->email,
            'tipo_tercero' => $validated['tipo_tercero'],
            'sucursal' => $validated['sucursal'],
            'tipodeidentificacion' => $validated['tipo_identificacion'],
            'numero_identificacion' => $validated['numero_identificacion'],
            'name' => $validated['name_client'],
            'lastname' => $validated['lastname_client'],
            'code_employee' => $employee->code_employee ?? '',
        ];

        // Enviar el formulario KYC
        $result = $this->kycService->enviarFormularioKyc($kycData);

        if ($result['success'] && isset($result['data'])) {
            $apiResponse = $result['data'];
            
            // Guardar la respuesta del API en la base de datos
            KycSend::create([
                'email' => $validated['email_client'],
                'name_client' => $validated['name_client'],
                'lastname_client' => $validated['lastname_client'],
                'shipping_status' => isset($apiResponse['status']) && $apiResponse['status'] === 'RECEIVED',
                'kyc_status' => $apiResponse['status'] ?? 'UNKNOWN',
                'status_firmante01' => 'WAITING',
                'status_firmante02' => 'WAITING',
                'employee_id' => $validated['employee_id'],
                'tracking_code' => $apiResponse['code'] ?? null,
                'client_code' => $validated['numero_identificacion'] ?? null,
            ]);

            return redirect()->route('dashboard')->with('success', 'Formulario KYC enviado exitosamente. Código de seguimiento: ' . ($apiResponse['code'] ?? 'N/A'));
        }

        return back()->withErrors([
            'kyc_error' => $result['error'] ?? 'Error al enviar el formulario KYC',
        ])->withInput();
    }
}
