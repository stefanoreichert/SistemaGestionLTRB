<form method="POST" action="{{ route('profile.update') }}">
    @csrf @method('PATCH')
    <div style="display:grid;gap:.875rem;">
        <div>
            <label style="font-size:.75rem;color:#9ca3af;display:block;margin-bottom:.3rem;">Nombre *</label>
            <input name="name" type="text" value="{{ old('name', $user->name) }}" required autocomplete="name"
                   style="width:100%;box-sizing:border-box;background:#1e1e1e;border:1px solid #2a2a2a;
                          color:#e5e7eb;border-radius:.5rem;padding:.5rem .75rem;font-size:.82rem;">
            @error('name')<p style="color:#f87171;font-size:.72rem;margin-top:.2rem;">{{ $message }}</p>@enderror
        </div>
        <div>
            <label style="font-size:.75rem;color:#9ca3af;display:block;margin-bottom:.3rem;">Email *</label>
            <input name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                   style="width:100%;box-sizing:border-box;background:#1e1e1e;border:1px solid #2a2a2a;
                          color:#e5e7eb;border-radius:.5rem;padding:.5rem .75rem;font-size:.82rem;">
            @error('email')<p style="color:#f87171;font-size:.72rem;margin-top:.2rem;">{{ $message }}</p>@enderror
        </div>
        <div style="display:flex;align-items:center;gap:1rem;">
            <button type="submit"
                    style="padding:.5rem 1.25rem;background:#d97706;color:#fff;border:none;
                           border-radius:.5rem;font-size:.82rem;font-weight:700;cursor:pointer;">
                Guardar
            </button>
            @if(session('status') === 'profile-updated')
            <span style="font-size:.78rem;color:#34d399;">&#10003; Guardado</span>
            @endif
        </div>
    </div>
</form>
