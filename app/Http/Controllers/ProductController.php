<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Devuelve todos los productos activos como JSON.
     * Usado por el panel izquierdo de la ventana de pedido (AJAX).
     */
    public function index()
    {
        $products = Product::active()
            ->orderBy('type')
            ->orderBy('name')
            ->get(['id', 'name', 'type', 'category', 'price', 'stock']);

        return response()->json($products);
    }
}
