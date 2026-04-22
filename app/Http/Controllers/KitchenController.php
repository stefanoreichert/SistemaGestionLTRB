<?php

namespace App\Http\Controllers;

use App\Events\KitchenStatusUpdated;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\KitchenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    public function __construct(protected KitchenService $kitchenService) {}

    /**
     * Vista principal de la cocina (KDS).
     * Solo muestra ítems de tipo 'Comida'. Las bebidas no aparecen en cocina.
     */
    public function index()
    {
        $orders = $this->kitchenService->getActiveOrders();

        return view('kitchen.index', compact('orders'));
    }

    /**
     * Toggle kitchen_status del pedido completo.
     * pendiente/en_proceso → listo, listo → en_proceso
     */
    public function updateOrderStatus(Order $order): JsonResponse
    {
        if (! $order->isOpen()) {
            return response()->json(['success' => false, 'message' => 'El pedido no está abierto.'], 422);
        }

        $newStatus = $this->kitchenService->toggleOrderStatus($order);

        return response()->json([
            'success'        => true,
            'data'           => ['kitchen_status' => $newStatus],
            'kitchen_status' => $newStatus,
        ]);
    }

    /**
     * Actualizar el estado de preparación de un ítem y persistirlo en DB.
     * Estados válidos: new → preparing → ready
     */
    public function updateItemStatus(Request $request, OrderItem $item): JsonResponse
    {
        $status = $request->input('status');

        if (! in_array($status, ['new', 'preparing', 'ready'], true)) {
            return response()->json(['success' => false, 'message' => 'Estado inválido.'], 422);
        }

        $this->kitchenService->updateItemStatus($item, $status);

        $order = $item->order()->with('table')->first();

        try {
            broadcast(new KitchenStatusUpdated(
                itemId:      $item->id,
                tableNumber: $order->table->number,
                orderId:     $order->id,
                status:      $status,
            ));
        } catch (\Throwable) {}

        return response()->json([
            'success' => true,
            'data'    => ['status' => $status],
            'status'  => $status,
        ]);
    }
}
