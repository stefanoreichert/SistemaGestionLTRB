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
     * Carga los pedidos abiertos; la actualización en vivo corre vía WebSocket.
     */
    public function index()
    {
        $orders = Order::with(['table', 'items.product'])
            ->where('status', 'open')
            ->orderBy('opened_at')
            ->get();

        // Inyectar estado de cocina desde Cache
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $item->kitchen_status = Cache::get("kitchen_item_{$item->id}", 'new');
            }
        }

        return view('kitchen.index', compact('orders'));
    }

    /**
     * Cambiar el estado de preparación de un ítem.
     * Estados válidos: new → preparing → ready
     */
    public function updateItemStatus(Request $request, OrderItem $item): JsonResponse
    {
        $status = $request->input('status');

        if (! in_array($status, ['new', 'preparing', 'ready'], true)) {
            return response()->json(['error' => 'Estado inválido.'], 422);
        }

        // Guardar en Cache por 10 horas (cubrir un turno completo)
        Cache::put("kitchen_item_{$item->id}", $status, now()->addHours(10));

        $order = $item->order()->with('table')->first();

        broadcast(new KitchenStatusUpdated(
            itemId:      $item->id,
            tableNumber: $order->table->number,
            orderId:     $order->id,
            status:      $status,
        ));

        return response()->json(['success' => true, 'status' => $status]);
    }
}
