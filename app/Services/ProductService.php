<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    /**
     * Obtener todos los productos activos ordenados por tipo y nombre.
     * Solo columnas necesarias para el catálogo del POS.
     */
    public function getActiveProducts(): Collection
    {
        return Product::active()
            ->orderBy('type')
            ->orderBy('name')
            ->get(['id', 'name', 'type', 'category', 'price', 'stock']);
    }
}
