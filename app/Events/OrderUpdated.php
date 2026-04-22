<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $payload;

    public function __construct(Order $order, string $action = 'updated')
    {
        $order->loadMissing(['table', 'items.product']);

        $this->payload = [
            'action'         => $action,
            'order_id'       => $order->id,
            'table_id'       => $order->table_id,
            'table_number'   => $order->table?->number,
            'is_delivery'    => (bool) $order->is_delivery,
            'delivery_label' => $order->delivery_label,
            'status'         => $order->status,
            'kitchen_status' => $order->kitchen_status,
            'total'          => $order->items->sum(fn($i) => $i->quantity * $i->unit_price),
            'items_count'    => $order->items->count(),
            'items'          => $order->items->map(fn($i) => [
                'id'         => $i->id,
                'name'       => $i->product->name,
                'type'       => $i->product->type ?? '',
                'category'   => $i->product->category ?? '',
                'quantity'   => $i->quantity,
                'unit_price' => $i->unit_price,
                'subtotal'   => $i->quantity * $i->unit_price,
                'notes'      => $i->notes ?? null,
            ])->values(),
            'opened_at'      => $order->opened_at?->toIso8601String(),
        ];
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('restaurant'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.updated';
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
