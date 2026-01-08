<?php

namespace App\Http\Controllers;

use App\Services\ClienteService;
use Illuminate\Http\Request;
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
}
