<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Collection;

class KitchenService
{
    /**
     * Obtener pedidos abiertos con SOLO ítems de tipo 'Comida' para el KDS.
     * Ordenes sin ítems de cocina se descartan.
     */
    public function getActiveOrders(): Collection
    {
        $orders = Order::with(['table', 'items.product'])
            ->where('status', 'open')
            ->orderBy('opened_at')
            ->get();

        foreach ($orders as $order) {
            $order->setRelation(
                'items',
                $order->items->filter(
                    fn($item) => $item->product && $item->product->type === 'Comida'
                )->values()
            );
        }

        return $orders->filter(fn($o) => $o->items->isNotEmpty())->values();
    }

    /**
     * Persistir el estado de preparación de un ítem en la base de datos.
     * Estados válidos: new → preparing → ready
     */
    public function updateItemStatus(OrderItem $item, string $status): void
    {
        $item->update(['kitchen_status' => $status]);
    }

    /**
     * Toggle kitchen_status del pedido completo.
     * pendiente / en_proceso → listo
     * listo → en_proceso
     */
    public function toggleOrderStatus(Order $order): string
    {
        $newStatus = $order->kitchen_status === 'listo' ? 'en_proceso' : 'listo';
        $order->update(['kitchen_status' => $newStatus]);
        return $newStatus;
    }
}
