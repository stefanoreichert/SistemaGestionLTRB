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

        <main>
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
    </body>
</html>
