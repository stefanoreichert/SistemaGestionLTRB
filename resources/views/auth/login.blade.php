<x-guest-layout>

    @if(session('status'))
        <div style="margin-bottom:1rem; padding:0.75rem 1rem; border-radius:0.5rem;
                    background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3);
                    color:#6ee7b7; font-size:0.8rem;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" style="display:flex; flex-direction:column; gap:1.25rem;">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="lt-label">Email</label>
            <input id="email" type="email" name="email"
                   class="lt-input"
                   value="{{ old('email') }}"
                   placeholder="usuario@lostroncos.com"
                   required autofocus autocomplete="username">
            @error('email')
                <p class="lt-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Contraseña --}}
        <div>
            <label for="password" class="lt-label">Contraseña</label>
            <input id="password" type="password" name="password"
                   class="lt-input"
                   placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                   required autocomplete="current-password">
            @error('password')
                <p class="lt-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Recordarme --}}
        <div style="display:flex; align-items:center; gap:0.5rem;">
            <input id="remember_me" type="checkbox" name="remember"
                   style="accent-color:#d97706; width:14px; height:14px; cursor:pointer;">
            <label for="remember_me" style="font-size:0.78rem; color:#6b7280; cursor:pointer;">
                Recordarme
            </label>
        </div>

        {{-- Botón --}}
        <button type="submit" class="lt-btn">
            Ingresar al Sistema
        </button>

    </form>

</x-guest-layout>
