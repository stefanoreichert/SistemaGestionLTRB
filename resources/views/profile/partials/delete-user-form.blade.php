<div>
    <p style="font-size:.78rem;color:#9ca3af;margin-bottom:1rem;line-height:1.5;">
        Al eliminar tu cuenta, todos tus datos se borrarán de forma permanente.
        Esta acción no se puede deshacer.
    </p>

    <button x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            style="padding:.5rem 1.25rem;background:rgba(220,38,38,.15);color:#f87171;border:1px solid rgba(220,38,38,.35);
                   border-radius:.5rem;font-size:.82rem;font-weight:700;cursor:pointer;">
        Eliminar mi cuenta
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="POST" action="{{ route('profile.destroy') }}"
              style="padding:1.5rem;background:#141414;">
            @csrf @method('DELETE')

            <div style="font-size:.95rem;font-weight:700;color:#f87171;margin-bottom:.5rem;">
                ¿Eliminar tu cuenta?
            </div>
            <p style="font-size:.78rem;color:#9ca3af;margin-bottom:1rem;line-height:1.5;">
                Esta acción es permanente. Ingresá tu contraseña para confirmar.
            </p>

            <input name="password" type="password" placeholder="Tu contraseña"
                   style="width:100%;box-sizing:border-box;background:#1e1e1e;border:1px solid #2a2a2a;
                          color:#e5e7eb;border-radius:.5rem;padding:.5rem .75rem;font-size:.82rem;margin-bottom:.5rem;">
            @error('password', 'userDeletion')
            <p style="color:#f87171;font-size:.72rem;margin-bottom:.75rem;">{{ $message }}</p>
            @enderror

            <div style="display:flex;justify-content:flex-end;gap:.75rem;margin-top:1rem;">
                <button type="button" x-on:click="$dispatch('close')"
                        style="padding:.45rem 1rem;background:#2a2a2a;color:#9ca3af;border:none;
                               border-radius:.5rem;font-size:.8rem;cursor:pointer;">
                    Cancelar
                </button>
                <button type="submit"
                        style="padding:.45rem 1rem;background:#dc2626;color:#fff;border:none;
                               border-radius:.5rem;font-size:.8rem;font-weight:700;cursor:pointer;">
                    Sí, eliminar
                </button>
            </div>
        </form>
    </x-modal>
</div>
