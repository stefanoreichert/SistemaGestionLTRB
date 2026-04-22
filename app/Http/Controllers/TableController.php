<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\JsonResponse;

class TableController extends Controller
{
    /**
     * Menú principal: grilla de 40 mesas con estado libre/ocupada.
     */
    public function index()
    {
        $tables   = Table::with('activeOrder')->orderBy('number')->get();
        $occupied = $tables->where('status', 'occupied')->count();
        $free     = $tables->where('status', 'free')->count();

        return view('tables.index', compact('tables', 'occupied', 'free'));
    }

    /**
     * API de polling: devuelve kitchen_status de todas las mesas con pedido abierto.
     */
    public function statuses(): JsonResponse
    {
        $tables = Table::with('activeOrder')->get();

        return response()->json(
            $tables->map(fn($table) => [
                'number'         => $table->number,
                'table_status'   => $table->status,
                'order_id'       => $table->activeOrder?->id,
                'kitchen_status' => $table->activeOrder?->kitchen_status ?? 'pendiente',
                'has_order'      => $table->activeOrder !== null,
            ])
        );
    }
}
