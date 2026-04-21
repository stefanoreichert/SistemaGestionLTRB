<x-app-layout>
    <x-slot name="header">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
            <div style="font-size:1.1rem;font-weight:800;color:#fbbf24;">Gestión de Usuarios</div>
            <a href="{{ route('admin.users.create') }}"
               style="padding:.5rem 1rem;background:#d97706;color:#fff;border-radius:.5rem;
                      font-size:.82rem;font-weight:700;text-decoration:none;">+ Nuevo Usuario</a>
        </div>
    </x-slot>

    @if(session('success'))
    <div style="margin:.75rem 1rem 0;padding:.6rem 1rem;background:rgba(5,150,105,.1);
                border:1px solid rgba(5,150,105,.3);border-radius:.5rem;color:#34d399;font-size:.82rem;">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="margin:.75rem 1rem 0;padding:.6rem 1rem;background:rgba(220,38,38,.08);
                border:1px solid rgba(220,38,38,.25);border-radius:.5rem;color:#f87171;font-size:.82rem;">
        {{ session('error') }}
    </div>
    @endif

    <div style="padding:.75rem 1rem;">
        <div style="background:#141414;border:1px solid #222;border-radius:.875rem;overflow:hidden;">
            <table style="width:100%;border-collapse:collapse;font-size:.82rem;">
                <thead>
                    <tr style="background:#1a1a1a;border-bottom:1px solid #222;">
                        <th style="text-align:left;padding:.6rem 1rem;color:#6b7280;font-weight:600;">Nombre</th>
                        <th style="text-align:left;padding:.6rem .75rem;color:#6b7280;font-weight:600;">Email</th>
                        <th style="text-align:center;padding:.6rem .75rem;color:#6b7280;font-weight:600;">Rol</th>
                        <th style="padding:.6rem 1rem;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                    @php
                        $roleStyle = match($u->role) {
                            'admin'  => 'background:rgba(217,119,6,.2);color:#fbbf24;',
                            'mozo'   => 'background:rgba(59,130,246,.2);color:#93c5fd;',
                            'cocina' => 'background:rgba(168,85,247,.2);color:#d8b4fe;',
                            default  => 'background:#1e1e1e;color:#6b7280;',
                        };
                    @endphp
                    <tr style="border-bottom:1px solid #1a1a1a;"
                        onmouseover="this.style.background='#1a1a1a'" onmouseout="this.style.background=''">
                        <td style="padding:.6rem 1rem;color:#e5e7eb;font-weight:500;">
                            {{ $u->name }}
                            @if($u->id === auth()->id())
                            <span style="font-size:.65rem;color:#6b7280;margin-left:.25rem;">(tú)</span>
                            @endif
                        </td>
                        <td style="padding:.6rem .75rem;color:#9ca3af;">{{ $u->email }}</td>
                        <td style="padding:.6rem .75rem;text-align:center;">
                            <span style="padding:.2rem .6rem;border-radius:.3rem;font-size:.72rem;font-weight:700;{{ $roleStyle }}">
                                {{ strtoupper($u->role) }}
                            </span>
                        </td>
                        <td style="padding:.6rem 1rem;text-align:right;white-space:nowrap;">
                            <a href="{{ route('admin.users.edit', $u) }}"
                               style="color:#d97706;font-size:.78rem;text-decoration:none;margin-right:.5rem;">Editar</a>
                            @if($u->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}" style="display:inline"
                                  onsubmit="return confirm('¿Eliminar al usuario {{ $u->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        style="background:none;border:none;color:#6b7280;font-size:.78rem;cursor:pointer;padding:0;"
                                        onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#6b7280'">Eliminar</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="padding:2rem;text-align:center;color:#4b5563;font-style:italic;">
                        Sin usuarios registrados.
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
