<?php

namespace App\Services;

use App\Models\Table;
use Illuminate\Support\Collection;

class TableService
{
    /**
     * Obtener todas las mesas con su pedido activo cargado (eager).
     */
    public function getAllWithActiveOrder(): Collection
    {
        return Table::with('activeOrder')->orderBy('number')->get();
    }

    /**
     * Devuelve el estado resumido de todas las mesas para el polling del frontend.
     */
    public function getStatuses(): Collection
    {
        return Table::with('activeOrder')
            ->get()
            ->map(fn(Table $table) => [
                'number'         => $table->number,
                'table_status'   => $table->status,
                'order_id'       => $table->activeOrder?->id,
                'kitchen_status' => $table->activeOrder?->kitchen_status ?? 'pendiente',
                'has_order'      => $table->activeOrder !== null,
            ]);
    }
}
