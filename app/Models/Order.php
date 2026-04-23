<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = ['table_id', 'user_id', 'total', 'status', 'kitchen_status', 'opened_at', 'closed_at', 'is_delivery', 'delivery_label', 'delivery_number'];

    protected $casts = [
        'total'       => 'decimal:2',
        'opened_at'   => 'datetime',
        'closed_at'   => 'datetime',
        'is_delivery' => 'boolean',
    ];

    public function isDelivery(): bool
    {
        return (bool) $this->is_delivery;
    }

    public function isKitchenReady(): bool
    {
        return $this->kitchen_status === 'listo';
    }

    public function isDelivered(): bool
    {
        return $this->kitchen_status === 'entregado';
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /** Total calculado desde los ítems (en tiempo real, antes de cierre). */
    public function calculateTotal(): float
    {
        return $this->items->sum(fn(OrderItem $item) => $item->quantity * $item->unit_price);
    }
}
