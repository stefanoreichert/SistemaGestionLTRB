<div style="display:grid;gap:1rem;">
    <div>
        <label style="font-size:.75rem;color:#9ca3af;display:block;margin-bottom:.3rem;">Nombre *</label>
        <input name="name" value="{{ old('name', $user->name ?? '') }}" required maxlength="100"
               style="width:100%;box-sizing:border-box;background:#1e1e1e;border:1px solid #2a2a2a;
                      color:#e5e7eb;border-radius:.5rem;padding:.5rem .75rem;font-size:.82rem;">
        @error('name')<p style="color:#f87171;font-size:.72rem;margin-top:.2rem;">{{ $message }}</p>@enderror
    </div>
    <div>
        <label style="font-size:.75rem;color:#9ca3af;display:block;margin-bottom:.3rem;">Email *</label>
        <input name="email" type="email" value="{{ old('email', $user->email ?? '') }}" required
               style="width:100%;box-sizing:border-box;background:#1e1e1e;border:1px solid #2a2a2a;
                      color:#e5e7eb;border-radius:.5rem;padding:.5rem .75rem;font-size:.82rem;">
        @error('email')<p style="color:#f87171;font-size:.72rem;margin-top:.2rem;">{{ $message }}</p>@enderror
    </div>
    <div>
        <label style="font-size:.75rem;color:#9ca3af;display:block;margin-bottom:.3rem;">Rol *</label>
        <select name="role" required
                style="width:100%;background:#1e1e1e;border:1px solid #2a2a2a;color:#e5e7eb;
                       border-radius:.5rem;padding:.5rem .75rem;font-size:.82rem;">
            <option value="">— Seleccionar —</option>
            <option value="admin"  {{ old('role', $user->role ?? '') === 'admin'  ? 'selected' : '' }}>Admin</option>
            <option value="mozo"   {{ old('role', $user->role ?? '') === 'mozo'   ? 'selected' : '' }}>Mozo</option>
            <option value="cocina" {{ old('role', $user->role ?? '') === 'cocina' ? 'selected' : '' }}>Cocina</option>
        </select>
        @error('role')<p style="color:#f87171;font-size:.72rem;margin-top:.2rem;">{{ $message }}</p>@enderror
    </div>
    <div>
        <label style="font-size:.75rem;color:#9ca3af;display:block;margin-bottom:.3rem;">
            Contraseña @isset($user)(dejar en blanco para no cambiar)@else *@endisset
        </label>
        <input name="password" type="password" @isset($user) @else required @endisset
               style="width:100%;box-sizing:border-box;background:#1e1e1e;border:1px solid #2a2a2a;
                      color:#e5e7eb;border-radius:.5rem;padding:.5rem .75rem;font-size:.82rem;">
        @error('password')<p style="color:#f87171;font-size:.72rem;margin-top:.2rem;">{{ $message }}</p>@enderror
    </div>
    <div>
        <label style="font-size:.75rem;color:#9ca3af;display:block;margin-bottom:.3rem;">Confirmar contraseña</label>
        <input name="password_confirmation" type="password"
               style="width:100%;box-sizing:border-box;background:#1e1e1e;border:1px solid #2a2a2a;
                      color:#e5e7eb;border-radius:.5rem;padding:.5rem .75rem;font-size:.82rem;">
    </div>
</div>
