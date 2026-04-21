<nav style="background:#111; border-bottom:1px solid #222; position:sticky; top:0; z-index:50;">
    <div style="padding:0 1.25rem;">
        <div style="display:flex; align-items:center; justify-content:space-between; height:3.5rem;">

            {{-- Brand --}}
            <a href="{{ route('tables.index') }}"
               style="display:flex; align-items:center; gap:0.6rem; text-decoration:none;">
                <span style="font-size:1.6rem; line-height:1;">🍽️</span>
                <div style="line-height:1.1;">
                    <div style="font-weight:800; color:#fbbf24; font-size:1rem; letter-spacing:0.03em;">Los Troncos</div>
                    <div style="font-size:0.6rem; color:#4b5563; letter-spacing:0.12em; text-transform:uppercase;">Restaurante</div>
                </div>
            </a>

            {{-- Nav links (desktop) --}}
            <div style="display:flex; align-items:center; gap:0.25rem;">
                @if(auth()->user()->isAdmin() || auth()->user()->isMozo())
                <a href="{{ route('tables.index') }}"
                   style="padding:0.45rem 0.9rem; border-radius:0.5rem; font-size:0.8rem; font-weight:600;
                          text-decoration:none; transition:all 0.15s;
                          {{ request()->routeIs('tables.index','orders.show')
                              ? 'background:#d97706; color:#fff;'
                              : 'color:#9ca3af; background:transparent;' }}">
                    🪑 Mesas
                </a>
                @endif

                @if(auth()->user()->isAdmin() || auth()->user()->isCocina())
                <a href="{{ route('kitchen') }}"
                   style="padding:0.45rem 0.9rem; border-radius:0.5rem; font-size:0.8rem; font-weight:600;
                          text-decoration:none; transition:all 0.15s;
                          {{ request()->routeIs('kitchen')
                              ? 'background:#d97706; color:#fff;'
                              : 'color:#9ca3af; background:transparent;' }}">
                    👨‍🍳 Cocina
                </a>
                @endif

                @if(auth()->user()->isAdmin())
                <a href="{{ route('reports.daily') }}"
                   style="padding:0.45rem 0.9rem; border-radius:0.5rem; font-size:0.8rem; font-weight:600;
                          text-decoration:none; transition:all 0.15s;
                          {{ request()->routeIs('reports.daily')
                              ? 'background:#d97706; color:#fff;'
                              : 'color:#9ca3af; background:transparent;' }}">
                    📊 Día
                </a>
                <a href="{{ route('reports.monthly') }}"
                   style="padding:0.45rem 0.9rem; border-radius:0.5rem; font-size:0.8rem; font-weight:600;
                          text-decoration:none; transition:all 0.15s;
                          {{ request()->routeIs('reports.monthly')
                              ? 'background:#d97706; color:#fff;'
                              : 'color:#9ca3af; background:transparent;' }}">
                    📅 Mes
                </a>
                <a href="{{ route('admin.products.index') }}"
                   style="padding:0.45rem 0.9rem; border-radius:0.5rem; font-size:0.8rem; font-weight:600;
                          text-decoration:none; transition:all 0.15s;
                          {{ request()->routeIs('admin.products.*')
                              ? 'background:#d97706; color:#fff;'
                              : 'color:#9ca3af; background:transparent;' }}">
                    📦 Productos
                </a>
                <a href="{{ route('admin.users.index') }}"
                   style="padding:0.45rem 0.9rem; border-radius:0.5rem; font-size:0.8rem; font-weight:600;
                          text-decoration:none; transition:all 0.15s;
                          {{ request()->routeIs('admin.users.*')
                              ? 'background:#d97706; color:#fff;'
                              : 'color:#9ca3af; background:transparent;' }}">
                    👥 Usuarios
                </a>
                @endif
            </div>

            {{-- User + Logout --}}
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <a href="{{ route('profile.edit') }}"
                   style="padding:0.4rem 0.85rem; border-radius:0.5rem; font-size:0.75rem;
                          font-weight:600; text-decoration:none; transition:all 0.15s;
                          {{ request()->routeIs('profile.edit','profile.update','password.update')
                              ? 'background:#d97706; color:#fff;'
                              : 'background:#1f1f1f; color:#9ca3af; border:1px solid #333;' }}">
                    👤 {{ Auth::user()->name }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            style="padding:0.4rem 0.85rem; border-radius:0.5rem; font-size:0.75rem;
                                   font-weight:600; background:#1f1f1f; color:#9ca3af;
                                   border:1px solid #333; cursor:pointer; transition:all 0.15s;"
                            onmouseover="this.style.background='#3f0f0f';this.style.color='#f87171';this.style.borderColor='#7f1d1d';"
                            onmouseout="this.style.background='#1f1f1f';this.style.color='#9ca3af';this.style.borderColor='#333';">
                        Salir
                    </button>
                </form>
            </div>

        </div>
    </div>
</nav>
