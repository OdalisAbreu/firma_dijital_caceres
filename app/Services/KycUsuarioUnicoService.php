<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class KycUsuarioUnicoService
{
    protected Client $client;
    protected string $baseUrl;
    protected string $username;
    protected string $password;

    public function __construct()
    {
        $this->baseUrl = config('services.kyc_api.base_url');
        $this->username = config('services.kyc_api.username');
        $this->password = config('services.kyc_api.password');

        // Asegurar que base_uri termine con / para que Guzzle concatene correctamente
        $baseUri = rtrim($this->baseUrl, '/') . '/';

        $this->client = new Client([
            'base_uri' => $baseUri,
            'timeout' => 60,
            'verify' => true,
        ]);
    }

    /**
     * Envía un formulario KYC
     *
     * @param array $data Datos del formulario:
     *   - title: Título del documento
     *   - description: Descripción del documento
     *   - name_client: Nombre del cliente
     *   - email_client: Email del cliente
     *   - name_employee: Nombre del empleado
     *   - email_employee: Email del empleado
     *   - tipo_tercero: Tipo de tercero
     *   - sucursal: Sucursal
     *   - tipodeidentificacion: Tipo de identificación
     *   - numero_identificacion: Número de identificación
     *   - name: Nombre (del cliente)
     *   - lastname: Apellido (del cliente)
     *   - code_employee: Código del empleado
     *
     * @return array Respuesta de la API
     */
    public function enviarFormularioKyc(array $data): array
    {
        try {
            $payload = $this->construirPayload($data);

            // Usar la ruta sin barra inicial para que Guzzle concatene correctamente con base_uri
            $response = $this->client->request('POST', 'set', [
                'auth' => [$this->username, $this->password],
                'json' => $payload,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();

            return [
                'success' => true,
                'status_code' => $statusCode,
                'data' => json_decode($body, true),
            ];
        } catch (GuzzleException $e) {
            Log::error('Error al enviar formulario KYC: ' . $e->getMessage(), [
                'data' => $data,
                'exception' => $e,
            ]);

            return [
                'success' => false,
                'status_code' => $e->getCode() ?: 500,
                'error' => $e->getMessage(),
                'data' => null,
            ];
        } catch (\Exception $e) {
            Log::error('Error inesperado en KycUsuarioUnicoService: ' . $e->getMessage(), [
                'data' => $data,
                'exception' => $e,
            ]);

            return [
                'success' => false,
                'status_code' => 500,
                'error' => 'Error inesperado: ' . $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * Construye el payload JSON con los datos proporcionados
     */
    protected function construirPayload(array $data): array
    {
        $tipo_formulario = $data['tipodepersona'] == 'fisica' ? 'formulario_conocimiento_persona_fisica' : 'formulario_conocimiento_persona_juridica';
        // Valores por defecto
        $title = $data['title'] ?? 'Formulario KYC';
        $description = $data['description'] ?? 'Documento para firma electrónica';
        $nameClient = $data['name_client'] ?? '';
        $emailClient = $data['email_client'] ?? '';
        $nameEmployee = $data['name_employee'] ?? '';
        $emailEmployee = $data['email_employee'] ?? '';
        $tipoTercero = $data['tipo_tercero'] ?? '';
        $sucursal = $data['sucursal'] ?? '';
        $tipoIdentificacion = $data['tipodeidentificacion'] ?? '';
        $numeroIdentificacion = $data['numero_identificacion'] ?? '';
        $name = $data['name'] ?? $nameClient;
        $lastname = $data['lastname'] ?? '';
        $codeEmployee = $data['code_employee'] ?? '';

        return [
            'groupCode' => 'caceresyasociados',
            'title' => $title,
            'description' => $description,
            'recipients' => [
                [
                    'key' => 'firmante01',
                    'name' => $nameClient,
                    'mail' => $emailClient,
                    'order' => 1,
                ],
                [
                    'key' => 'firmante02',
                    'name' => $nameEmployee,
                    'mail' => $emailEmployee,
                    'order' => 2,
                ],
            ],
            'customization' => [
                'requestMailSubject' => 'Documento listo para firmar',
                'requestMailBody' => "Hola {$name},\n\nPor favor revisa y firma el documento adjunto.\n\nSaludos.",
                'requestSmsBody' => 'Tienes un documento pendiente de firma. Por favor revisa tu correo.',
            ],
            'messages' => [
                [
                    'document' => [
                        'templateType' => 'pdf',
                        'templateCode' => $tipo_formulario,
                        'formRequired' => true,
                        'formDisabled' => false,
                        'formRecipientKey' => 'firmante01',
                    ],
                    'policies' => [
                        [
                            'evidences' => [
                                [
                                    'type' => 'GENERIC',
                                    'enabled' => true,
                                    'visible' => true,
                                    'helpText' => "OTP por email - {$name}",
                                    'recipientKey' => 'firmante01',
                                    'metadataList' => [
                                        [
                                            'key' => 'email',
                                            'value' => $emailClient,
                                            'internal' => false,
                                        ],
                                        [
                                            'key' => 'providerId',
                                            'value' => 'Mail',
                                            'internal' => false,
                                        ],
                                        [
                                            'key' => 'groupCode',
                                            'value' => 'groupCode-mail-firmante01',
                                            'internal' => false,
                                        ],
                                    ],
                                    'positions' => [
                                        [
                                            'page' => 1,
                                            'rectangle' => [
                                                'x' => 120,
                                                'y' => 90,
                                                'width' => 100,
                                                'height' => 60,
                                            ],
                                        ],
                                    ],
                                ],
                                [
                                    'type' => 'SIGNATURE',
                                    'enabled' => true,
                                    'visible' => true,
                                    'helpText' => 'Firma Interna Calidad',
                                    'recipientKey' => 'firmante02',
                                    'positions' => [
                                        [
                                            'page' => 1,
                                            'rectangle' => [
                                                'x' => 380,
                                                'y' => 90,
                                                'width' => 150,
                                                'height' => 80,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'signatures' => [
                                [
                                    'type' => 'SERVER',
                                    'helpText' => 'Sello del documento',
                                ],
                            ],
                        ],
                    ],
                    'metadataList' => $this->construirMetadataList($data, $tipoIdentificacion, $numeroIdentificacion, $name, $lastname, $emailClient, $codeEmployee, $tipoTercero, $sucursal),
                ],
            ],
        ];
    }

    /**
     * Construye la lista de metadata para el payload
     */
    protected function construirMetadataList(array $data, string $tipoIdentificacion, string $numeroIdentificacion, string $name, string $lastname, string $emailClient, string $codeEmployee, string $tipoTercero, string $sucursal): array
    {
        $metadataList = [];

        // Campos obligatorios o con valores por defecto
        $metadataList[] = ['key' => 'tipodeidentificacion', 'value' => $tipoIdentificacion];
        $metadataList[] = ['key' => 'numero', 'value' => $numeroIdentificacion];
        $metadataList[] = ['key' => 'nombreyapellidos', 'value' => trim("{$name} {$lastname}")];
        $metadataList[] = ['key' => 'correoelectronico', 'value' => $emailClient];
        $metadataList[] = ['key' => 'codigodelempleado', 'value' => $codeEmployee];
        $metadataList[] = ['key' => 'tipodetercero', 'value' => $tipoTercero];
        $metadataList[] = ['key' => 'sucursal', 'value' => $sucursal];

        // Campos opcionales - solo se agregan si están presentes en $data
        $optionalFields = [
            'fechadevencimiento',
            'sexo',
            'fechanacimiento',
            'ciudaddenacimiento',
            'provinciadenacimiento',
            'nacionalidad',
            'profesion',
            'ocupacioncargo',
            'empresa',
            'direcciondondelabora',
            'ciudad',
            'provincia',
            'telefono',
            'ciudadresidencia',
            'provinviaresidencia',
            'pais',
            'telefonoresidencia',
            'celular',
            'direccionresidencia',
            'sector',
            'direccionparaenviarproductos',
            'informacionactividadeconomica',
            'informacionfinanciera',
            'otrosingresos',
            'actividadeconomicadeotrosingresos',
            'recursospublicos',
            'respuestaafirmativauno',
            'poderpublico',
            'personareconocida',
            'afirmativaderespuesta',
            'solicituddeseguro',
            'fecha',
        ];

        foreach ($optionalFields as $field) {
            if (isset($data[$field]) && $data[$field] !== null && $data[$field] !== '') {
                $metadataList[] = [
                    'key' => $field,
                    'value' => $data[$field],
                ];
            }
        }

        return $metadataList;
    }

    /**
     * Consulta el estatus de un formulario KYC por su tracking code
     *
     * @param string $trackingCode Código de seguimiento del KYC
     * @return array Respuesta de la API con el estatus
     */
    public function consultarEstatus(string $trackingCode): array
    {
        try {
            // Construir la ruta para consultar el estatus
            $endpoint = "set/status/{$trackingCode}";

            $response = $this->client->request('GET', $endpoint, [
                'auth' => [$this->username, $this->password],
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();

            return [
                'success' => true,
                'status_code' => $statusCode,
                'data' => json_decode($body, true),
            ];
        } catch (GuzzleException $e) {
            Log::error('Error al consultar estatus KYC: ' . $e->getMessage(), [
                'tracking_code' => $trackingCode,
                'exception' => $e,
            ]);

            return [
                'success' => false,
                'status_code' => $e->getCode() ?: 500,
                'error' => $e->getMessage(),
                'data' => null,
            ];
        } catch (\Exception $e) {
            Log::error('Error inesperado al consultar estatus KYC: ' . $e->getMessage(), [
                'tracking_code' => $trackingCode,
                'exception' => $e,
            ]);

            return [
                'success' => false,
                'status_code' => 500,
                'error' => 'Error inesperado: ' . $e->getMessage(),
                'data' => null,
            ];
        }
    }
}
