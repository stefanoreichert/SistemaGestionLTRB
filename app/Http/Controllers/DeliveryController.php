<?php

namespace App\Http\Controllers;

use App\Events\OrderUpdated as OrderUpdatedEvent;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    /**
     * Dashboard de deliveries: muestra los 20 slots numerados.
     */
    public function index()
    {
        // Cargamos todos los deliveries activos (open) con delivery_number asignado
        $activeOrders = Order::where('is_delivery', true)
            ->where('status', 'open')
            ->whereNotNull('delivery_number')
            ->with('items')
            ->get()
            ->keyBy('delivery_number'); // [1 => Order, 5 => Order, ...]

        // Construimos los 20 slots
        $slots = collect(range(1, 20))->map(function (int $num) use ($activeOrders) {
            $order = $activeOrders->get($num);
            return [
                'number'         => $num,
                'order'          => $order,
                'is_free'        => $order === null,
                'kitchen_status' => $order?->kitchen_status ?? 'pendiente',
                'items_count'    => $order?->items->count() ?? 0,
                'total'          => $order?->items->sum(fn($i) => $i->quantity * $i->unit_price) ?? 0,
                'label'          => $order?->delivery_label ?? '',
                'opened_at'      => $order?->opened_at?->format('H:i') ?? '',
            ];
        });

        $free     = $slots->where('is_free', true)->count();
        $occupied = $slots->where('is_free', false)->count();

        return view('delivery.index', compact('slots', 'free', 'occupied'));
    }

    /**
     * Crear un nuevo pedido de delivery para un slot numerado (AJAX → JSON).
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'label'           => ['required', 'string', 'max:100'],
            'delivery_number' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        // Verificar que el slot esté libre
        $existing = Order::where('is_delivery', true)
            ->where('status', 'open')
            ->where('delivery_number', $request->delivery_number)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => "El slot Delivery #{$request->delivery_number} ya está ocupado.",
                'url'     => route('delivery.show', $existing),
            ], 409);
        }

        $order = $this->orderService->createDeliveryOrder(
            $request->label,
            (int) $request->delivery_number
        );

        return response()->json([
            'success'        => true,
            'order_id'       => $order->id,
            'delivery_label' => $order->delivery_label,
            'url'            => route('delivery.show', $order),
        ]);
    }

    /**
     * Vista de gestión de ítems del delivery (POS).
     * Reutiliza orders.show con modo delivery activo.
     */
    public function show(Order $order)
    {
        if (! $order->is_delivery || ! $order->isOpen()) {
            abort(404);
        }

        $order->load(['items.product']);
        $total = $order->items->sum(fn($i) => $i->quantity * $i->unit_price);

        return view('orders.show', [
            'table'           => null,
            'order'           => $order,
            'total'           => $total,
            'isDelivery'      => true,
            'deliveryLabel'   => $order->delivery_label,
            'deliveryNumber'  => $order->delivery_number,
            'addItemUrl'      => route('delivery.items.store', $order),
        ]);
    }

    /**
     * Resumen rápido del delivery para el modal (AJAX → JSON).
     */
    public function summary(Order $order): JsonResponse
    {
        if (! $order->is_delivery || ! $order->isOpen()) {
            return response()->json(['success' => false, 'message' => 'Pedido no disponible.']);
        }

        $order->load('items.product');

        if ($order->items->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Sin ítems aún.']);
        }

        return response()->json([
            'success' => true,
            'total'   => $order->items->sum(fn($i) => $i->quantity * $i->unit_price),
            'items'   => $order->items->map(fn($i) => [
                'name'     => $i->product->name,
                'quantity' => $i->quantity,
                'price'    => $i->unit_price,
                'subtotal' => $i->quantity * $i->unit_price,
            ]),
        ]);
    }

    /**
     * API: lista de deliveries activos con delivery_number (polling del panel).
     */
    public function apiIndex(): JsonResponse
    {
        $deliveries = Order::where('is_delivery', true)
            ->where('status', 'open')
            ->withCount('items')
            ->orderBy('delivery_number')
            ->get()
            ->map(fn($order) => [
                'order_id'        => $order->id,
                'delivery_label'  => $order->delivery_label,
                'delivery_number' => $order->delivery_number,
                'kitchen_status'  => $order->kitchen_status ?? 'pendiente',
                'items_count'     => $order->items_count,
                'opened_time'     => $order->opened_at?->format('H:i'),
                'opened_at'       => $order->opened_at?->toIso8601String(),
            ]);

        return response()->json($deliveries);
    }
}
