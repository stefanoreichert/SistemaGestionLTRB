<?php

namespace App\Http\Controllers;

use App\Services\ProductService;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService) {}

    /**
     * Devuelve todos los productos activos como JSON.
     * Usado por el panel izquierdo de la ventana de pedido (AJAX).
     */
    public function index()
    {
        return response()->json($this->productService->getActiveProducts());
    }
}
