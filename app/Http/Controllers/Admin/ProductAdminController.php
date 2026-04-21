<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductAdminController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('type')->orderBy('name')->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'name'     => 'required|string|max:120',
            'type'     => 'required|in:Comida,Bebida',
            'category' => 'required|string|max:80',
            'price'    => 'required|numeric|min:0',
            'stock'    => 'required|integer|min:0',
        ]);
        $data['active'] = $req->boolean('active', true);
        Product::create($data);
        return redirect()->route('admin.products.index')->with('success', 'Producto creado.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $req, Product $product)
    {
        $data = $req->validate([
            'name'     => 'required|string|max:120',
            'type'     => 'required|in:Comida,Bebida',
            'category' => 'required|string|max:80',
            'price'    => 'required|numeric|min:0',
            'stock'    => 'required|integer|min:0',
        ]);
        $data['active'] = $req->boolean('active', true);
        $product->update($data);
        return redirect()->route('admin.products.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Producto eliminado.');
    }
}