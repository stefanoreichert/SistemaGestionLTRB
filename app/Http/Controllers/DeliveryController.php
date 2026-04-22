<?php

namespace App\Http\Controllers;

use App\Events\OrderUpdated as OrderUpdatedEvent;
use App\Http\Requests\AddOrderItemRequest;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    /**
     * Crear un nuevo pedido de delivery (AJAX → JSON).
     * El frontend redirige inmediatamente a la vista de gestión de ítems.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'label' => ['required', 'string', 'max:100'],
        ]);

        $order = $this->orderService->createDeliveryOrder($request->label);

        return response()->json([
            'success'        => true,
            'order_id'       => $order->id,
            'delivery_label' => $order->delivery_label,
            'url'            => route('deliveries.show', $order),
        ]);
    }

    /**
     * Vista de gestión de ítems del delivery.
     * Reutiliza la vista orders.show con modo delivery activo.
     */
    public function show(Order $order)
    {
        if (! $order->is_delivery || ! $order->isOpen()) {
            abort(404);
        }

        $order->load(['items.product']);
        $total = $order->items->sum(fn($i) => $i->quantity * $i->unit_price);

        return view('orders.show', [
            'table'         => null,
            'order'         => $order,
            'total'         => $total,
            'isDelivery'    => true,
            'deliveryLabel' => $order->delivery_label,
            'addItemUrl'    => route('delivery-items.store', $order),
        ]);
    }

    /**
     * API: lista de deliveries activos (para polling del panel de mesas).
     */
    public function apiIndex(): JsonResponse
    {
        $deliveries = Order::where('is_delivery', true)
            ->where('status', 'open')
            ->withCount('items')
            ->orderBy('opened_at')
            ->get()
            ->map(fn($order) => [
                'order_id'       => $order->id,
                'delivery_label' => $order->delivery_label,
                'kitchen_status' => $order->kitchen_status ?? 'pendiente',
                'items_count'    => $order->items_count,
                'opened_time'    => $order->opened_at?->format('H:i'),
                'opened_at'      => $order->opened_at?->toIso8601String(),
            ]);

        return response()->json($deliveries);
    }
}
