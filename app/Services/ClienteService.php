<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class ClienteService
{
    protected Client $client;
    protected string $baseUrl;
    protected string $username;
    protected string $password;

    public function __construct()
    {
        $this->baseUrl = config('services.clientes_api.base_url');
        $this->username = config('services.clientes_api.username');
        $this->password = config('services.clientes_api.password');

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
            'verify' => false, // Cambiar a true en producción con certificados válidos
        ]);
    }

    /**
     * Obtiene la lista de clientes con filtros opcionales
     *
     * @param array $filters Filtros opcionales:
     *   - page: número de página (default: 1)
     *   - page_size: tamaño de página (default: 10)
     *   - cnomcliente: nombre del cliente
     *   - crnc: RNC
     *   - ccedula: cédula
     *   - tipo_cliente: tipo de cliente (PERSONAL, etc.)
     *   - estatus: estatus (A, etc.)
     *   - sucursal: sucursal
     *   - es_prospecto: si es prospecto (C, etc.)
     *
     * @return array Respuesta de la API
     * @throws \Exception
     */
    public function getClientes(array $filters = []): array
    {
        try {
            // Valores por defecto
            $queryParams = [
                'page' => $filters['page'] ?? 1,
                'page_size' => $filters['page_size'] ?? 10,
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
                if (isset($filters[$filter]) && $filters[$filter] !== null && $filters[$filter] !== '') {
                    $queryParams[$filter] = $filters[$filter];
                }
            }

            $response = $this->client->request('GET', '/api/clientes', [
                'auth' => [$this->username, $this->password],
                'query' => $queryParams,
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
            Log::error('Error al consumir API de clientes: ' . $e->getMessage(), [
                'filters' => $filters,
                'exception' => $e,
            ]);

            return [
                'success' => false,
                'status_code' => $e->getCode() ?: 500,
                'error' => $e->getMessage(),
                'data' => null,
            ];
        } catch (\Exception $e) {
            Log::error('Error inesperado en ClienteService: ' . $e->getMessage(), [
                'filters' => $filters,
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
