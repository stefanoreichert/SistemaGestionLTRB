<?php

namespace App\Http\Controllers;

use App\Events\OrderUpdated as OrderUpdatedEvent;
use App\Http\Requests\AddOrderItemRequest;
use App\Http\Requests\UpdateOrderItemRequest;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Table;
use App\Services\OrderService;

class OrderItemController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    /**
     * Agregar un ítem al pedido de una mesa.
     * Crea el pedido automáticamente si no existe (primer ítem = apertura de mesa).
     * Responde JSON para el frontend AJAX.
     */
    public function store(AddOrderItemRequest $request, Table $table)
    {
        $product = Product::findOrFail($request->product_id);
        $order   = $this->orderService->getOrCreateOpenOrder($table);
        $item    = $this->orderService->addItem($order, $product, $request->quantity);

        try { broadcast(new OrderUpdatedEvent($order->fresh(['table', 'items.product']), 'updated')); } catch (\Throwable) {}

        return response()->json([
            'success' => true,
            'message' => "'{$product->name}' agregado al pedido.",
            'item'    => [
                'id'         => $item->id,
                'product'    => $product->name,
                'quantity'   => $item->quantity,
                'unit_price' => $item->unit_price,
                'subtotal'   => $item->quantity * $item->unit_price,                'notes'      => $item->notes,            ],
        ]);
    }

    /**
     * Actualizar la cantidad de un ítem existente.
     * Responde JSON con el nuevo subtotal para actualizar el DOM sin recargar.
     */
    public function update(UpdateOrderItemRequest $request, OrderItem $item)
    {
        if (! $item->order->isOpen()) {
            return response()->json([
                'success' => false,
                'message' => 'El pedido ya está cerrado.',
            ], 422);
        }

        $item  = $this->orderService->updateItem($item, $request->quantity);

        // Actualizar notas si se envían
        if ($request->has('notes')) {
            $item->update(['notes' => $request->notes]);
        }

        $order = $item->order()->with(['table', 'items.product'])->first();

        try { broadcast(new OrderUpdatedEvent($order, 'updated')); } catch (\Throwable) {}

        return response()->json([
            'success'  => true,
            'quantity' => $item->quantity,
            'subtotal' => $item->quantity * $item->unit_price,
        ]);
    }

    /**
     * Actualizar solo la nota de un ítem, sin tocar la cantidad.
     */
    public function updateNote(OrderItem $item): \Illuminate\Http\JsonResponse
    {
        if (! $item->order->isOpen()) {
            return response()->json(['success' => false, 'message' => 'El pedido ya está cerrado.'], 422);
        }

        $note = request()->input('notes', '');
        $item->update(['notes' => $note ?: null]);

        return response()->json(['success' => true]);
    }

    /**
     * Eliminar un ítem del pedido.
     * Si queda sin ítems, la mesa se libera automáticamente en el servicio.
     */
    public function destroy(OrderItem $item)
    {
        if (! $item->order->isOpen()) {
            return response()->json([
                'success' => false,
                'message' => 'El pedido ya está cerrado.',
            ], 422);
        }

        $order = $item->order()->with(['table', 'items.product'])->first();
        $this->orderService->removeItem($item);
        $order->refresh();

        try { broadcast(new OrderUpdatedEvent($order, 'updated')); } catch (\Throwable) {}

        return response()->json(['success' => true]);
    }
}

