<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Se emite cuando se actualiza el estado de preparación de un ítem en cocina.
 * El estado se persiste en Cache (no requiere cambio de esquema DB).
 */
class KitchenStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int     $itemId,
        public readonly ?int    $tableNumber,
        public readonly int     $orderId,
        public readonly string  $status,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('restaurant'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'kitchen.status';
    }

    public function broadcastWith(): array
    {
        return [
            'item_id'      => $this->itemId,
            'table_number' => $this->tableNumber,
            'order_id'     => $this->orderId,
            'status'       => $this->status,
        ];
    }
}
