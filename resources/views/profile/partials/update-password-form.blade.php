<form method="POST" action="{{ route('password.update') }}">
    @csrf @method('PUT')
    <div style="display:grid;gap:.875rem;">
        <div>
            <label style="font-size:.75rem;color:#9ca3af;display:block;margin-bottom:.3rem;">Contraseña actual *</label>
            <input name="current_password" type="password" autocomplete="current-password"
                   style="width:100%;box-sizing:border-box;background:#1e1e1e;border:1px solid #2a2a2a;
                          color:#e5e7eb;border-radius:.5rem;padding:.5rem .75rem;font-size:.82rem;">
            @error('current_password', 'updatePassword')
            <p style="color:#f87171;font-size:.72rem;margin-top:.2rem;">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label style="font-size:.75rem;color:#9ca3af;display:block;margin-bottom:.3rem;">Nueva contraseña *</label>
            <input name="password" type="password" autocomplete="new-password"
                   style="width:100%;box-sizing:border-box;background:#1e1e1e;border:1px solid #2a2a2a;
                          color:#e5e7eb;border-radius:.5rem;padding:.5rem .75rem;font-size:.82rem;">
            @error('password', 'updatePassword')
            <p style="color:#f87171;font-size:.72rem;margin-top:.2rem;">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label style="font-size:.75rem;color:#9ca3af;display:block;margin-bottom:.3rem;">Confirmar contraseña *</label>
            <input name="password_confirmation" type="password" autocomplete="new-password"
                   style="width:100%;box-sizing:border-box;background:#1e1e1e;border:1px solid #2a2a2a;
                          color:#e5e7eb;border-radius:.5rem;padding:.5rem .75rem;font-size:.82rem;">
        </div>
        <div style="display:flex;align-items:center;gap:1rem;">
            <button type="submit"
                    style="padding:.5rem 1.25rem;background:#d97706;color:#fff;border:none;
                           border-radius:.5rem;font-size:.82rem;font-weight:700;cursor:pointer;">
                Actualizar contraseña
            </button>
            @if(session('status') === 'password-updated')
            <span style="font-size:.78rem;color:#34d399;">&#10003; Contraseña actualizada</span>
            @endif
        </div>
    </div>
</form>
