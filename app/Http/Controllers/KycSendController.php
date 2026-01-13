<?php

namespace App\Http\Controllers;

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
        return Inertia::render('KycSend/Create');
    }

    /**
     * Store a newly created KYC send.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'tipo_persona' => 'required|in:fisica,juridica',
            'name_client' => 'required|string|max:255',
            'lastname_client' => 'required|string|max:255',
            'email_client' => 'required|email|max:255',
            'tipo_identificacion' => 'required|in:Cédula,RNC,Pasaporte',
            'numero_identificacion' => 'required|string|max:255',
            'tipo_tercero' => 'required|in:Tomador,Asegurado,Beneficiario,Afianzado,Proveedor,Empleado,Apoderado',
            'sucursal' => 'required|in:Principal,Romana,Punta Cana',
            'fechadevencimiento' => 'nullable|string|max:255',
            'sexo' => 'nullable|in:M,F',
            'fechanacimiento' => 'nullable|string|max:255',
            'ciudaddenacimiento' => 'nullable|string|max:255',
            'provinciadenacimiento' => 'nullable|string|max:255',
            'nacionalidad' => 'nullable|string|max:255',
            'profesion' => 'nullable|string|max:255',
            'ocupacioncargo' => 'nullable|string|max:255',
            'empresa' => 'nullable|string|max:255',
            'direcciondondelabora' => 'nullable|string|max:255',
            'ciudad' => 'nullable|string|max:255',
            'provincia' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:255',
            'ciudadresidencia' => 'nullable|string|max:255',
            'provinviaresidencia' => 'nullable|string|max:255',
            'pais' => 'nullable|string|max:255',
            'telefonoresidencia' => 'nullable|string|max:255',
            'celular' => 'nullable|string|max:255',
            'direccionresidencia' => 'nullable|string|max:255',
            'sector' => 'nullable|string|max:255',
            'direccionparaenviarproductos' => 'nullable|string|max:255',
            'informacionactividadeconomica' => 'nullable|string|max:255',
            'otroactividadeconomica' => 'nullable|string|max:255',
            'informacionfinanciera' => 'nullable|string|max:255',
            'otrosingresos' => 'nullable|string|max:255',
            'actividadeconomicadeotrosingresos' => 'nullable|string|max:255',
            'recursospublicos' => 'nullable|in:Si,No',
            'respuestaafirmativauno' => 'nullable|string|max:255',
            'poderpublico' => 'nullable|in:Si,No',
            'personareconocida' => 'nullable|in:Si,No',
            'afirmativaderespuesta' => 'nullable|in:Si,No',
            'solicituddeseguro' => 'nullable|string|max:255',
            'fecha' => 'nullable|string|max:255',
        ]);

        // Obtener el usuario autenticado
        $user = auth()->user();

        // Preparar datos para el servicio
        $kycData = [
            'title' => $validated['title'] ?? 'Formulario KYC - ' . $validated['name_client'] . ' ' . $validated['lastname_client'],
            'description' => $validated['description'] ?? 'Documento para firma electrónica',
            'tipodepersona' => $validated['tipo_persona'],
            'name_client' => $validated['name_client'],
            'lastname_client' => $validated['lastname_client'],
            'email_client' => $validated['email_client'],
            'name_employee' => $user->name,
            'email_employee' => $user->email,
            'tipo_tercero' => $validated['tipo_tercero'],
            'sucursal' => $validated['sucursal'],
            'tipodeidentificacion' => $validated['tipo_identificacion'],
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
            'informacionactividadeconomica' => $request->informacionactividadeconomica === 'Otro' && $request->otroactividadeconomica ? $request->otroactividadeconomica : $request->informacionactividadeconomica,
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
                'user_id' => auth()->id(),
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
