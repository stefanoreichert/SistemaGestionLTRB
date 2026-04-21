<x-app-layout>
    <x-slot name="header">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
            <div style="font-size:1.1rem;font-weight:800;color:#fbbf24;">Gestión de Productos</div>
            <a href="{{ route('admin.products.create') }}"
               style="padding:.5rem 1rem;background:#d97706;color:#fff;border-radius:.5rem;
                      font-size:.82rem;font-weight:700;text-decoration:none;">+ Nuevo Producto</a>
        </div>
    </x-slot>

    @if(session('success'))
    <div style="margin:.75rem 1rem 0;padding:.6rem 1rem;background:rgba(5,150,105,.1);
                border:1px solid rgba(5,150,105,.3);border-radius:.5rem;color:#34d399;font-size:.82rem;">
        {{ session('success') }}
    </div>
    @endif

    <div style="padding:.75rem 1rem;">
        <div style="background:#141414;border:1px solid #222;border-radius:.875rem;overflow:hidden;">
            <table style="width:100%;border-collapse:collapse;font-size:.82rem;">
                <thead>
                    <tr style="background:#1a1a1a;border-bottom:1px solid #222;">
                        <th style="text-align:left;padding:.6rem 1rem;color:#6b7280;font-weight:600;">Nombre</th>
                        <th style="text-align:left;padding:.6rem .75rem;color:#6b7280;font-weight:600;">Tipo</th>
                        <th style="text-align:left;padding:.6rem .75rem;color:#6b7280;font-weight:600;">Categoría</th>
                        <th style="text-align:right;padding:.6rem .75rem;color:#6b7280;font-weight:600;">Precio</th>
                        <th style="text-align:center;padding:.6rem .75rem;color:#6b7280;font-weight:600;">Stock</th>
                        <th style="text-align:center;padding:.6rem .75rem;color:#6b7280;font-weight:600;">Activo</th>
                        <th style="padding:.6rem 1rem;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $p)
                    <tr style="border-bottom:1px solid #1a1a1a;"
                        onmouseover="this.style.background='#1a1a1a'" onmouseout="this.style.background=''">
                        <td style="padding:.6rem 1rem;color:#e5e7eb;font-weight:500;">{{ $p->name }}</td>
                        <td style="padding:.6rem .75rem;color:#9ca3af;">{{ $p->type }}</td>
                        <td style="padding:.6rem .75rem;color:#9ca3af;">{{ $p->category }}</td>
                        <td style="padding:.6rem .75rem;text-align:right;color:#4ade80;font-weight:700;">
                            ${{ number_format($p->price, 0, ',', '.') }}
                        </td>
                        <td style="padding:.6rem .75rem;text-align:center;
                                   color:{{ $p->stock <= 5 ? '#f97316' : '#6b7280' }};">
                            {{ $p->stock }}
                        </td>
                        <td style="padding:.6rem .75rem;text-align:center;">
                            <span style="padding:.15rem .5rem;border-radius:.25rem;font-size:.7rem;font-weight:700;
                                         background:{{ $p->active ? 'rgba(5,150,105,.2)' : 'rgba(100,100,100,.2)' }};
                                         color:{{ $p->active ? '#34d399' : '#6b7280' }};">
                                {{ $p->active ? 'SI' : 'NO' }}
                            </span>
                        </td>
                        <td style="padding:.6rem 1rem;text-align:right;white-space:nowrap;">
                            <a href="{{ route('admin.products.edit', $p) }}"
                               style="color:#d97706;font-size:.78rem;text-decoration:none;margin-right:.5rem;">Editar</a>
                            <form method="POST" action="{{ route('admin.products.destroy', $p) }}" style="display:inline"
                                  onsubmit="return confirm('¿Eliminar este producto?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        style="background:none;border:none;color:#6b7280;font-size:.78rem;cursor:pointer;padding:0;"
                                        onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#6b7280'">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="padding:2rem;text-align:center;color:#4b5563;font-style:italic;">
                        Sin productos registrados.
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
