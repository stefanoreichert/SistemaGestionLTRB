<nav style="background:#111; border-bottom:1px solid #222; position:sticky; top:0; z-index:50;">
    <style>
        @media (max-width:767px) {
            .nav-desktop { display:none !important; }
            .nav-user-desktop { display:none !important; }
            .nav-hamburger { display:flex !important; }
        }
        @media (min-width:768px) {
            .nav-mobile-menu { display:none !important; }
            .nav-hamburger { display:none !important; }
        }
        .nav-mobile-menu {
            background:#111; border-top:1px solid #222;
            padding:.75rem 1.25rem; display:none; flex-direction:column; gap:.35rem;
        }
        .nav-mobile-menu.open { display:flex !important; }
        .nav-mobile-link {
            display:block; padding:.65rem .875rem; border-radius:.5rem;
            font-size:.9rem; font-weight:600; text-decoration:none;
            color:#9ca3af; transition:all .15s;
        }
        .nav-mobile-link:hover { background:#1e1e1e; color:#e5e7eb; }
        .nav-mobile-link.active { background:#d97706; color:#fff; }
        .nav-mobile-divider { height:1px; background:#222; margin:.35rem 0; }
    </style>

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
            <div class="nav-desktop" style="display:flex; align-items:center; gap:0.25rem;">
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

            {{-- User + Logout (desktop) --}}
            <div class="nav-user-desktop" style="display:flex; align-items:center; gap:0.75rem;">
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

            {{-- Hamburger (mobile) --}}
            <button class="nav-hamburger"
                    id="nav-hamburger-btn"
                    onclick="document.getElementById('nav-mobile-menu').classList.toggle('open')"
                    style="display:none; align-items:center; justify-content:center;
                           background:#1f1f1f; border:1px solid #333; border-radius:.5rem;
                           padding:.5rem .65rem; cursor:pointer; color:#9ca3af; font-size:1.25rem;
                           line-height:1;">
                ☰
            </button>

        </div>
    </div>

    {{-- Mobile menu --}}
    <div id="nav-mobile-menu" class="nav-mobile-menu">
        @if(auth()->user()->isAdmin() || auth()->user()->isMozo())
        <a href="{{ route('tables.index') }}"
           class="nav-mobile-link {{ request()->routeIs('tables.index','orders.show') ? 'active' : '' }}">
            🪑 Mesas
        </a>
        @endif

        @if(auth()->user()->isAdmin() || auth()->user()->isCocina())
        <a href="{{ route('kitchen') }}"
           class="nav-mobile-link {{ request()->routeIs('kitchen') ? 'active' : '' }}">
            👨‍🍳 Cocina
        </a>
        @endif

        @if(auth()->user()->isAdmin())
        <a href="{{ route('reports.daily') }}"
           class="nav-mobile-link {{ request()->routeIs('reports.daily') ? 'active' : '' }}">
            📊 Reporte del Día
        </a>
        <a href="{{ route('reports.monthly') }}"
           class="nav-mobile-link {{ request()->routeIs('reports.monthly') ? 'active' : '' }}">
            📅 Reporte Mensual
        </a>
        <a href="{{ route('admin.products.index') }}"
           class="nav-mobile-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            📦 Productos
        </a>
        <a href="{{ route('admin.users.index') }}"
           class="nav-mobile-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            👥 Usuarios
        </a>
        @endif

        <div class="nav-mobile-divider"></div>

        <a href="{{ route('profile.edit') }}"
           class="nav-mobile-link {{ request()->routeIs('profile.edit','profile.update','password.update') ? 'active' : '' }}">
            👤 {{ Auth::user()->name }}
            <span style="font-size:.7rem; color:#6b7280; margin-left:.4rem;">{{ strtoupper(Auth::user()->role) }}</span>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-mobile-link"
                    style="width:100%; text-align:left; background:none; border:none;
                           cursor:pointer; font-family:inherit;">
                🚪 Cerrar sesión
            </button>
        </form>
    </div>
</nav>
