<x-app-layout>
    <x-slot name="header">
        <div style="display:flex;align-items:center;gap:.75rem;">
            <span style="font-size:1.1rem;font-weight:800;color:#fbbf24;">Mi Perfil</span>
            <span style="font-size:.75rem;color:#6b7280;">{{ auth()->user()->name }}</span>
            <span style="padding:.15rem .5rem;border-radius:.3rem;font-size:.7rem;font-weight:700;
                         background:{{ auth()->user()->isAdmin() ? 'rgba(217,119,6,.2)' : (auth()->user()->isCocina() ? 'rgba(168,85,247,.2)' : 'rgba(59,130,246,.2)') }};
                         color:{{ auth()->user()->isAdmin() ? '#fbbf24' : (auth()->user()->isCocina() ? '#d8b4fe' : '#93c5fd') }}">
                {{ strtoupper(auth()->user()->role) }}
            </span>
        </div>
    </x-slot>

    <div style="padding:.75rem 1rem;max-width:42rem;display:flex;flex-direction:column;gap:1rem;">

        {{-- Información personal --}}
        <div style="background:#141414;border:1px solid #222;border-radius:.875rem;padding:1.5rem;">
            <div style="margin-bottom:1rem;">
                <div style="font-size:.9rem;font-weight:700;color:#e5e7eb;">Información de la cuenta</div>
                <div style="font-size:.75rem;color:#6b7280;margin-top:.2rem;">Cambiá tu nombre y email.</div>
            </div>
            @include('profile.partials.update-profile-information-form')
        </div>

        {{-- Contraseña --}}
        <div style="background:#141414;border:1px solid #222;border-radius:.875rem;padding:1.5rem;">
            <div style="margin-bottom:1rem;">
                <div style="font-size:.9rem;font-weight:700;color:#e5e7eb;">Cambiar contraseña</div>
                <div style="font-size:.75rem;color:#6b7280;margin-top:.2rem;">Usá una contraseña segura y única.</div>
            </div>
            @include('profile.partials.update-password-form')
        </div>

        {{-- Eliminar cuenta --}}
        <div style="background:#141414;border:1px solid rgba(220,38,38,.2);border-radius:.875rem;padding:1.5rem;">
            <div style="margin-bottom:1rem;">
                <div style="font-size:.9rem;font-weight:700;color:#f87171;">Zona de peligro</div>
                <div style="font-size:.75rem;color:#6b7280;margin-top:.2rem;">La eliminación de la cuenta es permanente e irreversible.</div>
            </div>
            @include('profile.partials.delete-user-form')
        </div>

    </div>
</x-app-layout>
