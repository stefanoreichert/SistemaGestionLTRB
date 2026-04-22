<?php

namespace App\Http\Controllers;

use App\Services\TableService;
use Illuminate\Http\JsonResponse;

class TableController extends Controller
{
    public function __construct(protected TableService $tableService) {}

    /**
     * Menú principal: grilla de mesas con estado libre/ocupada.
     */
    public function index()
    {
        $tables   = $this->tableService->getAllWithActiveOrder();
        $occupied = $tables->where('status', 'occupied')->count();
        $free     = $tables->where('status', 'free')->count();

        return view('tables.index', compact('tables', 'occupied', 'free'));
    }

    /**
     * API de polling: devuelve kitchen_status de todas las mesas con pedido abierto.
     */
    public function statuses(): JsonResponse
    {
        return response()->json($this->tableService->getStatuses());
    }
}
