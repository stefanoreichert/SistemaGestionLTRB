<?php

namespace App\Http\Controllers;

use App\Events\KitchenStatusUpdated;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class KitchenController extends Controller
{
    /**
     * Vista principal de la cocina (KDS).
     * Solo muestra ítems de tipo 'Comida'. Las bebidas no aparecen en cocina.
     */
    public function index()
    {
        $orders = Order::with(['table', 'items.product'])
            ->where('status', 'open')
            ->orderBy('opened_at')
            ->get();

        // Filtrar ítems: solo Comida; descartar órdenes sin ítems de cocina
        foreach ($orders as $order) {
            $order->setRelation(
                'items',
                $order->items->filter(
                    fn($item) => $item->product && $item->product->type === 'Comida'
                )->values()
            );
        }

        $orders = $orders->filter(fn($o) => $o->items->isNotEmpty())->values();

        return view('kitchen.index', compact('orders'));
    }

    /**
     * Actualizar kitchen_status de un pedido completo.
     * Toggle: pendiente/en_proceso → listo, listo → en_proceso
     */
    public function updateOrderStatus(Order $order): JsonResponse
    {
        if (! $order->isOpen()) {
            return response()->json(['error' => 'El pedido no está abierto.'], 422);
        }

        $newStatus = $order->kitchen_status === 'listo' ? 'en_proceso' : 'listo';
        $order->update(['kitchen_status' => $newStatus]);

        return response()->json([
            'success'        => true,
            'kitchen_status' => $newStatus,
        ]);
    }

    /**
     * Cambiar el estado de preparación de un ítem (sistema legado de cache).
     * Estados válidos: new → preparing → ready
     */
    public function updateItemStatus(Request $request, OrderItem $item): JsonResponse
    {
        $status = $request->input('status');

        if (! in_array($status, ['new', 'preparing', 'ready'], true)) {
            return response()->json(['error' => 'Estado inválido.'], 422);
        }

        Cache::put("kitchen_item_{$item->id}", $status, now()->addHours(10));

        $order = $item->order()->with('table')->first();

        try {
            broadcast(new KitchenStatusUpdated(
                itemId:      $item->id,
                tableNumber: $order->table->number,
                orderId:     $order->id,
                status:      $status,
            ));
        } catch (\Throwable) {}

        return response()->json(['success' => true, 'status' => $status]);
    }
}
