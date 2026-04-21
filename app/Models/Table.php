<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    protected $fillable = ['number', 'capacity', 'status'];

    /**
     * Usar `number` como clave de ruta → /tables/5/order = mesa número 5.
     */
    public function getRouteKeyName(): string
    {
        return 'number';
    }

    /**
     * El pedido activo (open) de esta mesa, si existe.
     */
    public function activeOrder(): HasOne
    {
        return $this->hasOne(Order::class)->where('status', 'open');
    }

    /**
     * Todos los pedidos históricos de esta mesa.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function isOccupied(): bool
    {
        return $this->status === 'occupied';
    }

    public function isFree(): bool
    {
        return $this->status === 'free';
    }
}
