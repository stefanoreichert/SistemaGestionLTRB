<?php

// ── ProductAdminController ────────────────────────────────────────────────
file_put_contents(__DIR__ . '/app/Http/Controllers/Admin/ProductAdminController.php', <<<'PHP'
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
PHP);

// ── Admin/UserController ──────────────────────────────────────────────────
file_put_contents(__DIR__ . '/app/Http/Controllers/Admin/UserController.php', <<<'PHP'
<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $req)
    {
        $req->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'role'     => 'required|in:admin,mozo,cocina',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        User::create([
            'name'     => $req->name,
            'email'    => $req->email,
            'role'     => $req->role,
            'password' => Hash::make($req->password),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $req, User $user)
    {
        $req->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role'     => 'required|in:admin,mozo,cocina',
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $user->name  = $req->name;
        $user->email = $req->email;
        $user->role  = $req->role;
        if ($req->filled('password')) {
            $user->password = Hash::make($req->password);
        }
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'No puedes eliminarte a ti mismo.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado.');
    }
}
PHP);

echo "Controllers escritos OK\n";
