<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Los Troncos &mdash; Acceso</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *{box-sizing:border-box;}
        body{margin:0;font-family:'Figtree',sans-serif;background:#0a0a0a;}
        .ll-left{
            background:radial-gradient(ellipse at 30% 50%,#1c1108 0%,#0a0a0a 70%);
            position:relative;overflow:hidden;
        }
        .ll-left::before{
            content:'';position:absolute;inset:0;
            background-image:linear-gradient(rgba(217,119,6,.05) 1px,transparent 1px),linear-gradient(90deg,rgba(217,119,6,.05) 1px,transparent 1px);
            background-size:48px 48px;
        }
        .ll-right{
            background:#0d0d0d;
            display:flex;flex-direction:column;
            justify-content:center;align-items:center;
            padding:3rem 2.5rem;
        }
        .lt-input{
            background:rgba(255,255,255,.04);border:1px solid rgba(217,119,6,.22);
            color:#f9fafb;width:100%;border-radius:.625rem;
            padding:.75rem 1rem;font-size:.875rem;font-family:'Figtree',sans-serif;
            transition:border-color .2s,box-shadow .2s;
        }
        .lt-input:focus{border-color:#d97706;box-shadow:0 0 0 3px rgba(217,119,6,.18);outline:none;}
        .lt-input::placeholder{color:#374151;}
        .lt-btn{
            background:linear-gradient(135deg,#d97706 0%,#92400e 100%);
            color:#fff;width:100%;padding:.85rem;
            border-radius:.625rem;font-weight:700;font-size:.875rem;
            font-family:'Figtree',sans-serif;letter-spacing:.05em;
            border:none;cursor:pointer;transition:all .2s;
        }
        .lt-btn:hover{
            background:linear-gradient(135deg,#f59e0b 0%,#d97706 100%);
            transform:translateY(-1px);box-shadow:0 6px 20px rgba(217,119,6,.35);
        }
        .lt-btn:active{transform:translateY(0);}
        .lt-label{display:block;font-size:.68rem;font-weight:700;letter-spacing:.12em;
            text-transform:uppercase;color:#6b7280;margin-bottom:.45rem;}
        .lt-error{color:#f87171;font-size:.75rem;margin-top:.35rem;}
        @keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
        .lt-anim{animation:fadeUp .45s ease both;}
        @media(max-width:768px){.ll-left{display:none!important;}.ll-right{min-height:100vh!important;width:100%!important;}}
    </style>
</head>
<body>
<div style="display:flex;min-height:100vh;">

    {{-- ── Izquierda: Branding ──────────────────────────── --}}
    <div class="ll-left" style="flex:1;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:3.5rem 3rem;position:relative;">
        <div style="position:relative;z-index:2;text-align:center;max-width:24rem;">

            <div style="font-size:5.5rem;line-height:1;margin-bottom:1.25rem;filter:drop-shadow(0 0 40px rgba(217,119,6,.5));">
                🍽️
            </div>

            <h1 style="font-size:3rem;font-weight:800;color:#fbbf24;line-height:1;margin:0 0 .4rem;">
                Los Troncos
            </h1>
            <p style="font-size:.7rem;color:#d97706;letter-spacing:.22em;text-transform:uppercase;margin:0 0 2rem;font-weight:600;">
                Restaurante
            </p>

            <div style="width:60px;height:2px;background:linear-gradient(90deg,transparent,#d97706,transparent);margin:0 auto 2rem;"></div>

            <p style="color:#4b5563;font-size:.85rem;line-height:1.75;margin:0 0 2.25rem;">
                Sistema de Gestión POS<br>para mozos y administradores.
            </p>

            <div style="display:flex;flex-direction:column;gap:.7rem;text-align:left;">
                @foreach([
                    ['🪑','Gestión de 40 mesas en tiempo real'],
                    ['🧭','Pedidos y tickets en segundos'],
                    ['📊','Reportes diarios y mensuales'],
                    ['👨‍🍳','Vista de cocina en vivo'],
                ] as [$ico,$txt])
                    <div style="display:flex;align-items:center;gap:.7rem;font-size:.82rem;color:#6b7280;">
                        <span style="color:#d97706;font-size:1.1rem;">{{ $ico }}</span>
                        <span>{{ $txt }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div style="position:absolute;bottom:1.5rem;font-size:.68rem;color:#1f2937;letter-spacing:.08em;">
            &copy; {{ date('Y') }} Los Troncos Restaurante
        </div>
    </div>

    {{-- ── Derecha: Formulario ─────────────────────────── --}}
    <div class="ll-right" style="width:min(460px,100%);flex-shrink:0;">
        <div class="lt-anim" style="width:100%;max-width:360px;">

            <div style="margin-bottom:2.25rem;text-align:center;">
                <div style="font-size:.68rem;color:#6b7280;letter-spacing:.2em;text-transform:uppercase;margin-bottom:.4rem;">Bienvenido</div>
                <h2 style="font-size:1.6rem;font-weight:800;color:#fbbf24;margin:0;">Iniciar Sesión</h2>
                <p style="font-size:.78rem;color:#4b5563;margin-top:.35rem;">Sistema de Gestión POS</p>
            </div>

            {{ $slot }}

        </div>
        <p style="font-size:.68rem;color:#1f2937;margin-top:2rem;">Los Troncos &copy; {{ date('Y') }}</p>
    </div>

</div>
</body>
</html>
