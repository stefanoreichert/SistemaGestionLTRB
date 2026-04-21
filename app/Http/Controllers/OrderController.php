<?php

namespace App\Http\Controllers;

use App\Events\OrderUpdated as OrderUpdatedEvent;
use App\Models\Order;
use App\Models\Table;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    /**
     * Ventana de pedido de una mesa.
     * Carga el pedido abierto si existe; si no, lo crea al agregar el primer ítem.
     */
    public function show(Table $table)
    {
        $order = Order::where('table_id', $table->id)
            ->where('status', 'open')
            ->with(['items.product'])
            ->first();

        $total = $order
            ? $order->items->sum(fn($i) => $i->quantity * $i->unit_price)
            : 0;

        return view('orders.show', compact('table', 'order', 'total'));
    }

    /**
     * Resumen rápido de la mesa para el modal del menú principal (AJAX → JSON).
     */
    public function summary(Table $table)
    {
        $order = Order::where('table_id', $table->id)
            ->where('status', 'open')
            ->with(['items.product'])
            ->first();

        if (! $order || $order->items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'La mesa no tiene pedidos activos.',
            ]);
        }

        return response()->json([
            'success' => true,
            'mesa'    => $table->number,
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
     * Cerrar mesa: descontar stock, calcular total, marcar cerrada, liberar mesa.
     * Toda la lógica crítica corre en una única transacción (Bug #1 y #4 del legado).
     * Redirige al ticket imprimible.
     */
    public function close(Order $order)
    {
        if (! $order->isOpen()) {
            return redirect()->route('tables.index')
                ->with('error', 'El pedido ya no está abierto.');
        }

        if ($order->items()->count() === 0) {
            return back()->with('error', 'No se puede cerrar una mesa sin ítems.');
        }

        try {
            $this->orderService->closeOrder($order);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        broadcast(new OrderUpdatedEvent($order->fresh(['table', 'items.product']), 'closed'));

        return redirect()->route('tickets.show', $order)
            ->with('success', 'Mesa cerrada correctamente.');
    }

    /**
     * Cancelar / borrar pedido SIN descontar stock.
     * Equivalente a "Borrar Pedido" del sistema original.
     */
    public function cancel(Order $order)
    {
        if (! $order->isOpen()) {
            return redirect()->route('tables.index')
                ->with('error', 'El pedido ya no está abierto.');
        }

        $mesa    = $order->table->number;
        $payload = ['table_number' => $mesa, 'table_id' => $order->table_id, 'order_id' => $order->id];

        $this->orderService->cancelOrder($order);

        broadcast(new OrderUpdatedEvent(
            $order->setRelation('table', $order->table)->setRelation('items', collect()),
            'cancelled'
        ));

        return redirect()->route('tables.index')
            ->with('success', "Pedido de Mesa {$mesa} cancelado.");
    }
}
