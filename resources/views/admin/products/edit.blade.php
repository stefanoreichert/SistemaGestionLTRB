<x-app-layout>
    <x-slot name="header">
        <div style="display:flex;align-items:center;gap:.875rem;">
            <a href="{{ route('admin.products.index') }}"
               style="color:#6b7280;text-decoration:none;font-size:.78rem;padding:.3rem .65rem;
                      border:1px solid #2a2a2a;border-radius:.5rem;">&#8592; Productos</a>
            <span style="font-size:1rem;font-weight:800;color:#fbbf24;">Editar Producto</span>
        </div>
    </x-slot>
    <div style="padding:.75rem 1rem;max-width:36rem;">
        <div style="background:#141414;border:1px solid #222;border-radius:.875rem;padding:1.5rem;">
            <form method="POST" action="{{ route('admin.products.update', $product) }}">
                @csrf @method('PUT')
                @include('admin.products._form', ['product' => $product])
                <div style="display:flex;gap:.5rem;justify-content:flex-end;margin-top:1.25rem;">
                    <a href="{{ route('admin.products.index') }}"
                       style="padding:.5rem 1rem;background:#1e1e1e;color:#9ca3af;border:1px solid #333;
                              border-radius:.5rem;font-size:.82rem;text-decoration:none;">Cancelar</a>
                    <button type="submit"
                            style="padding:.5rem 1.25rem;background:#d97706;color:#fff;border:none;
                                   border-radius:.5rem;font-size:.82rem;font-weight:700;cursor:pointer;">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
