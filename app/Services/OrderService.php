<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Table;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Obtener el pedido abierto de una mesa, o crear uno nuevo.
     * Marca la mesa como ocupada si se crea un pedido nuevo.
     */
    public function getOrCreateOpenOrder(Table $table): Order
    {
        $order = Order::where('table_id', $table->id)
            ->where('status', 'open')
            ->first();

        if (! $order) {
            $order = Order::create([
                'table_id'  => $table->id,
                'user_id'   => auth()->id(),
                'status'    => 'open',
                'total'     => 0,
                'opened_at' => now(),
            ]);

            $table->update(['status' => 'occupied']);
        }

        return $order;
    }

    /**
     * Agregar un producto al pedido.
     * Regla de negocio 7.3: si el producto ya existe en el pedido,
     * se SUMA la cantidad (no se inserta una nueva fila).
     */
    public function addItem(Order $order, Product $product, int $quantity): OrderItem
    {
        $existing = $order->items()->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->increment('quantity', $quantity);
            return $existing->fresh(['product']);
        }

        return $order->items()->create([
            'product_id' => $product->id,
            'quantity'   => $quantity,
            'unit_price' => $product->price, // snapshot del precio actual
        ]);
    }

    /**
     * Actualizar la cantidad de un ítem.
     * La validación de quantity >= 1 viene del FormRequest.
     */
    public function updateItem(OrderItem $item, int $quantity): OrderItem
    {
        $item->update(['quantity' => $quantity]);
        return $item->fresh();
    }

    /**
     * Eliminar un ítem del pedido.
     * Si el pedido queda sin ítems, libera la mesa automáticamente.
     */
    public function removeItem(OrderItem $item): void
    {
        $order = $item->order()->with('table')->first();
        $item->delete();

        if ($order && $order->items()->count() === 0 && $order->table) {
            $order->table->update(['status' => 'free']);
        }
    }

    /**
     * Cancelar un pedido SIN descontar stock (equivalente a "Borrar Pedido").
     * Los ítems se eliminan en cascada por la FK de order_items.
     * No genera ticket.
     */
    public function cancelOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->update([
                'status'    => 'cancelled',
                'closed_at' => now(),
            ]);
            if ($order->table) {
                $order->table->update(['status' => 'free']);
            }
        });
    }

    /**
     * Cerrar un pedido: validar stock → descontar stock → calcular total →
     * marcar como cerrado → liberar mesa (si aplica).
     * Todo en una única transacción atómica.
     */
    public function closeOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->load('items.product', 'table');

            // ── 1. Validar stock de TODOS los ítems antes de tocar nada ─────
            foreach ($order->items as $item) {
                if ($item->product->stock < $item->quantity) {
                    throw new \RuntimeException(
                        "Stock insuficiente para: {$item->product->name}. " .
                        "Disponible: {$item->product->stock}, Pedido: {$item->quantity}."
                    );
                }
            }

            // ── 2. Descontar stock ──────────────────────────────────────────
            foreach ($order->items as $item) {
                $item->product->decrement('stock', $item->quantity);
            }

            // ── 3. Calcular total ───────────────────────────────────────────
            $total = $order->items->sum(
                fn(OrderItem $i) => $i->quantity * $i->unit_price
            );

            // ── 4. Cerrar el pedido ─────────────────────────────────────────
            $order->update([
                'status'    => 'closed',
                'total'     => $total,
                'closed_at' => now(),
            ]);

            // ── 5. Liberar la mesa (solo si el pedido tiene mesa física) ────
            if ($order->table) {
                $order->table->update(['status' => 'free']);
            }
        });
    }

    /**
     * Crear un pedido de delivery sin mesa física.
     * El pedido queda abierto; los ítems se agregan con addItem().
     */
    public function createDeliveryOrder(string $label, ?int $deliveryNumber = null): Order
    {
        return Order::create([
            'table_id'        => null,
            'user_id'         => auth()->id(),
            'status'          => 'open',
            'total'           => 0,
            'is_delivery'     => true,
            'delivery_label'  => $label,
            'delivery_number' => $deliveryNumber,
            'opened_at'       => now(),
        ]);
    }
}
