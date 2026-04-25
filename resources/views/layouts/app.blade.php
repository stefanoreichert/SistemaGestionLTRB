<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Los Troncos' }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased" style="background:#0d0d0d; color:#e5e7eb; min-height:100vh;">
        @include('layouts.navigation')

        @isset($header)
            <header style="background:#111111; border-bottom:1px solid #222; padding:0.75rem 1.25rem;">
                <div style="max-width:100%; margin:0 auto;">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main id="spa-content" style="transition:opacity 0.1s ease;">
            {{ $slot }}
        </main>

        <footer style="background:#111;border-top:1px solid #1e1e1e;margin-top:auto;padding:1.5rem 1.25rem 1rem;">
            <div style="max-width:80rem;margin:0 auto;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1rem;">

                {{-- Marca --}}
                <div style="display:flex;align-items:center;gap:.6rem;">
                    <span style="font-size:1.3rem;line-height:1;">🍽️</span>
                    <div style="line-height:1.15;">
                        <div style="font-weight:800;color:#fbbf24;font-size:.85rem;letter-spacing:.03em;">Los Troncos</div>
                        <div style="font-size:.6rem;color:#374151;letter-spacing:.12em;text-transform:uppercase;">Sistema de Gestión</div>
                    </div>
                </div>

                {{-- Info central --}}
                <div style="display:flex;gap:1.5rem;flex-wrap:wrap;">
                    <div style="display:flex;align-items:center;gap:.4rem;font-size:.72rem;color:#4b5563;">
                        <span style="color:#374151;">⚡</span>
                        Laravel 12 &nbsp;·&nbsp; PHP 8.2 &nbsp;·&nbsp; WebSockets
                    </div>
                    <div style="display:flex;align-items:center;gap:.4rem;font-size:.72rem;color:#4b5563;">
                        <span style="color:#374151;">👤</span>
                        {{ Auth::user()->name }}
                        <span style="padding:.1rem .45rem;border-radius:.25rem;font-size:.62rem;font-weight:700;
                                     background:{{ Auth::user()->isAdmin() ? 'rgba(217,119,6,.15)' : (Auth::user()->isCocina() ? 'rgba(168,85,247,.15)' : 'rgba(59,130,246,.15)') }};
                                     color:{{ Auth::user()->isAdmin() ? '#fbbf24' : (Auth::user()->isCocina() ? '#d8b4fe' : '#93c5fd') }};">
                            {{ strtoupper(Auth::user()->role) }}
                        </span>
                    </div>
                </div>

                {{-- Copyright --}}
                <div style="font-size:.68rem;color:#374151;">
                    &copy; {{ date('Y') }} Restaurante Los Troncos
                </div>

            </div>
        </footer>
        <!-- SPA progress bar -->
        <div id="_spa-bar" style="position:fixed;top:0;left:0;height:2px;width:0;z-index:9999;
             background:linear-gradient(90deg,#6366f1,#8b5cf6);opacity:0;pointer-events:none;
             transition:width .4s ease,opacity .25s ease;"></div>

    </body>

    <script>
    /* ── SPA Navigation (Mesas ↔ Delivery) ─────────────────────────────── */
    (function () {
        'use strict';

        /* Cleanup registry: cada vista registra su limpieza aquí */
        var _cleanup = null;
        window.spaRegisterCleanup = function (fn) { _cleanup = fn; };
        function _runCleanup() {
            if (typeof _cleanup === 'function') { try { _cleanup(); } catch (e) {} }
            _cleanup = null;
        }

        /* Barra de progreso */
        function _barStart() {
            var b = document.getElementById('_spa-bar');
            if (!b) return;
            b.style.transition = 'none'; b.style.width = '0%'; b.style.opacity = '1';
            b.offsetWidth; // force reflow
            b.style.transition = 'width .4s ease'; b.style.width = '65%';
        }
        function _barDone() {
            var b = document.getElementById('_spa-bar');
            if (!b) return;
            b.style.width = '100%';
            setTimeout(function () { b.style.opacity = '0'; setTimeout(function () { b.style.width = '0%'; }, 250); }, 180);
        }

        /* Función principal de navegación */
        async function navigate(url, push) {
            if (typeof url !== 'string') return;
            _runCleanup();
            _barStart();
            try {
                var res = await fetch(url, {
                    headers: { 'Accept': 'text/html', 'X-SPA': '1' },
                    credentials: 'same-origin'
                });
                if (!res.ok) throw new Error('bad-response');

                var html = await res.text();
                var doc  = (new DOMParser()).parseFromString(html, 'text/html');
                var newMain = doc.getElementById('spa-content');
                if (!newMain) throw new Error('no-spa-content');

                document.title = doc.title || document.title;

                /* Actualizar CSRF token por si la sesión rotó */
                var newCsrf = doc.querySelector('meta[name="csrf-token"]');
                if (newCsrf) {
                    var headCsrf = document.querySelector('meta[name="csrf-token"]');
                    if (headCsrf) headCsrf.setAttribute('content', newCsrf.getAttribute('content'));
                    if (window.axios) window.axios.defaults.headers.common['X-CSRF-TOKEN'] = newCsrf.getAttribute('content');
                }

                var main = document.getElementById('spa-content');
                main.style.opacity = '0';
                await new Promise(function (r) { setTimeout(r, 90); });

                main.innerHTML = newMain.innerHTML;

                /* Re-ejecutar <script> del nuevo contenido */
                main.querySelectorAll('script').forEach(function (old) {
                    var s = document.createElement('script');
                    Array.from(old.attributes).forEach(function (a) { s.setAttribute(a.name, a.value); });
                    s.textContent = old.textContent;
                    old.replaceWith(s);
                });

                if (push !== false) history.pushState({ spaUrl: url }, '', url);
                _updateNavActive(url);
                main.style.opacity = '1';

            } catch (err) {
                window.location.href = url;
                return;
            } finally {
                _barDone();
            }
        }

        /* Actualizar estado activo en la navegación */
        function _updateNavActive(url) {
            var path = '';
            try { path = new URL(url, location.origin).pathname; } catch (e) { return; }

            document.querySelectorAll('[data-spa-path]').forEach(function (el) {
                var check  = el.dataset.spaPath;
                var active = (path === check || path.startsWith(check + '/'));
                var cls    = el.dataset.spaActiveClass;
                if (cls) {
                    el.classList.toggle(cls, active);
                    el.classList.toggle('spa-inactive', !active);
                }
            });
        }

        /* Interceptar clicks en links SPA */
        document.addEventListener('click', function (e) {
            var a = e.target.closest('a[data-spa]');
            if (!a || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;
            try {
                var u = new URL(a.href);
                if (u.origin !== location.origin) return;
                if (u.pathname === location.pathname) return;
            } catch (err) { return; }
            e.preventDefault();
            navigate(a.href);
        }, true);

        /* Navegación con botones Atrás/Adelante */
        window.addEventListener('popstate', function (e) {
            navigate((e.state && e.state.spaUrl) ? e.state.spaUrl : location.href, false);
        });

        /* Guardar estado inicial */
        history.replaceState({ spaUrl: location.href }, '', location.href);
        window.spaNavigate = navigate;

        /* Actualizar nav en carga inicial */
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () { _updateNavActive(location.href); });
        } else {
            _updateNavActive(location.href);
        }
    })();
    </script>
</html>
