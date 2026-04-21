<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    protected $fillable = ['name', 'type', 'category', 'price', 'stock', 'active'];

    protected $casts = [
        'price'  => 'decimal:2',
        'stock'  => 'integer',
        'active' => 'boolean',
    ];

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** Scope: solo productos activos */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function hasStock(int $quantity = 1): bool
    {
        return $this->stock >= $quantity;
    }
}
