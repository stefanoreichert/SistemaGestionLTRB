<x-app-layout>
    <x-slot name="header">
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.75rem;">

            <div style="display:flex; align-items:center; gap:0.5rem;">
                <div style="display:flex; align-items:center; gap:0.5rem; padding:0.35rem 0.85rem;
                            border-radius:0.5rem; background:rgba(5,150,105,0.15); border:1px solid rgba(5,150,105,0.3);">
                    <span id="dot-free" style="width:8px; height:8px; border-radius:50%; background:#10b981;
                                 animation:pulse 2s cubic-bezier(0.4,0,0.6,1) infinite;"></span>
                    <span id="lbl-free" style="color:#34d399; font-size:0.8rem; font-weight:700;">{{ $free }} libres</span>
                </div>
                <div style="display:flex; align-items:center; gap:0.5rem; padding:0.35rem 0.85rem;
                            border-radius:0.5rem; background:rgba(220,38,38,0.12); border:1px solid rgba(220,38,38,0.25);">
                    <span style="width:8px; height:8px; border-radius:50%; background:#ef4444;"></span>
                    <span id="lbl-occupied" style="color:#f87171; font-size:0.8rem; font-weight:700;">{{ $occupied }} ocupadas</span>
                </div>
            </div>

            <div style="display:flex; align-items:center; gap:0.35rem;">
                <span id="ws-indicator" style="width:8px; height:8px; border-radius:50%; background:#4b5563;"
                      title="Estado WebSocket"></span>
                <span style="font-size:0.7rem; color:#4b5563;" id="ws-label">Conectando&hellip;</span>
            </div>

            <a href="{{ route('delivery.index') }}"
               data-spa="true"
               style="padding:.4rem .9rem; background:rgba(139,92,246,.18); color:#c4b5fd;
                      border:1px solid rgba(139,92,246,.45); border-radius:.5rem;
                      font-size:.78rem; font-weight:700; cursor:pointer; transition:all .15s;
                      text-decoration:none; display:inline-block;"
               onmouseover="this.style.background='rgba(139,92,246,.32)'"
               onmouseout="this.style.background='rgba(139,92,246,.18)'">
                + Nuevo Delivery
            </a>

        </div>
    </x-slot>

    <style>
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
        .mesa-btn {
            position: relative;
            border-radius: 1rem;
            border-width: 2px;
            border-style: solid;
            padding: 1.1rem 0.5rem;
            text-align: center;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s, border-color 0.15s, background 0.15s;
            user-select: none;
            background: none;
            width: 100%;
            display: block;
        }
        .mesa-btn:active { transform: scale(0.93) !important; }
        .mesa-libre {
            background: #0d3d27;
            border-color: #10b981;
            color: #6ee7b7;
            box-shadow: 0 3px 12px rgba(0,0,0,0.7), 0 0 0 1px rgba(16,185,129,0.2);
        }
        .mesa-libre:hover {
            background: #0f4d30;
            border-color: #34d399;
            transform: scale(1.05);
            box-shadow: 0 6px 24px rgba(16,185,129,0.35), 0 3px 12px rgba(0,0,0,0.7);
        }
        .mesa-ocupada {
            background: #3d0a0a;
            border-color: #ef4444;
            color: #fca5a5;
            box-shadow: 0 3px 12px rgba(0,0,0,0.7), 0 0 0 1px rgba(239,68,68,0.2);
        }
        .mesa-ocupada:hover {
            background: #4d0f0f;
            border-color: #f87171;
            transform: scale(1.05);
            box-shadow: 0 6px 24px rgba(239,68,68,0.35), 0 3px 12px rgba(0,0,0,0.7);
        }
        .mesa-lista {
            background: #3d2800;
            border-color: #eab308;
            color: #fef08a;
            box-shadow: 0 3px 12px rgba(0,0,0,0.7), 0 0 0 1px rgba(234,179,8,0.2);
        }
        .mesa-lista:hover {
            background: #4d3300;
            border-color: #fbbf24;
            transform: scale(1.05);
            box-shadow: 0 6px 24px rgba(234,179,8,0.35), 0 3px 12px rgba(0,0,0,0.7);
        }
        .mesa-num  { font-size: 1.6rem; font-weight: 800; line-height: 1; }
        .mesa-estado {
            font-size: 0.6rem; font-weight: 600; letter-spacing: 0.08em;
            text-transform: uppercase; margin-top: 0.3rem; opacity: 0.7;
        }
        .mesa-dot {
            position: absolute; top: 0.45rem; right: 0.5rem;
            width: 7px; height: 7px; border-radius: 50%;
        }
        /* BotÃ³n entregar dentro de la tarjeta */
        .btn-entregar {
            display: block; width: calc(100% + 0px);
            margin-top: 0.45rem; padding: 0.3rem 0.4rem;
            font-size: 0.58rem; font-weight: 700; letter-spacing: .05em;
            text-transform: uppercase; cursor: pointer;
            background: rgba(234,179,8,.22); color: #fef08a;
            border: 1px solid rgba(234,179,8,.5); border-radius: 0.35rem;
            transition: background .15s;
        }
        .btn-entregar:hover { background: rgba(234,179,8,.38); }
        .btn-entregar-hidden { display: none !important; }

        /* â”€â”€ Delivery cards â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
        .delivery-card {
            border-radius:.875rem; border:2px solid rgba(139,92,246,.45);
            padding:.875rem .75rem; background:rgba(60,20,120,.28);
            color:#c4b5fd; cursor:pointer; transition:transform .15s,box-shadow .15s;
            user-select:none;
        }
        .delivery-card:hover {
            background:rgba(60,20,120,.5); border-color:#8b5cf6;
            transform:scale(1.04); box-shadow:0 0 18px rgba(139,92,246,.3);
        }
        .delivery-label { font-size:1rem; font-weight:800; line-height:1.1; }
        .delivery-meta  { font-size:.62rem; color:#a78bfa; margin-top:.25rem;
                          text-transform:uppercase; letter-spacing:.07em; }
        .btn-del-entregar {
            display:block; width:100%; margin-top:.45rem; padding:.3rem .4rem;
            font-size:.58rem; font-weight:700; letter-spacing:.05em;
            text-transform:uppercase; cursor:pointer;
            background:rgba(139,92,246,.22); color:#c4b5fd;
            border:1px solid rgba(139,92,246,.5); border-radius:.35rem;
            transition:background .15s;
        }
        .btn-del-entregar:hover { background:rgba(139,92,246,.4); }
        .btn-del-entregar-hidden { display:none !important; }
    </style>

    <div style="padding:2rem;">

        @if(session('success'))
            <div style="margin-bottom:1rem; padding:0.75rem 1rem; border-radius:0.5rem;
                        background:rgba(5,150,105,0.12); border:1px solid rgba(5,150,105,0.3);
                        color:#6ee7b7; font-size:0.82rem;">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="margin-bottom:1rem; padding:0.75rem 1rem; border-radius:0.5rem;
                        background:rgba(220,38,38,0.1); border:1px solid rgba(220,38,38,0.3);
                        color:#f87171; font-size:0.82rem;">
                {{ session('error') }}
            </div>
        @endif

        {{-- Grilla de mesas --}}
        <div id="mesas-grid" style="display:grid; grid-template-columns:repeat(3, 1fr); gap:1.25rem;"
             class="mesas-grid">
            @foreach($tables as $table)
                @php
                    $libre = $table->isFree();
                    $ks    = $table->activeOrder?->kitchen_status ?? 'pendiente';
                    $isLista = !$libre && $ks === 'listo';
                    $cardClass = $libre ? 'mesa-libre' : ($isLista ? 'mesa-lista' : 'mesa-ocupada');
                    $dotColor  = $libre ? '#10b981' : ($isLista ? '#eab308' : '#ef4444');
                    $orderId   = $table->activeOrder?->id;
                @endphp
                <div
                    id="mesa-btn-{{ $table->number }}"
                    class="mesa-btn {{ $cardClass }}"
                    data-kitchen-status="{{ $ks }}"
                    data-order-id="{{ $orderId ?? '' }}"
                    onclick="abrirMesa({{ $table->number }})"
                    ondblclick="verResumen({{ $table->number }}, event)"
                    title="Mesa {{ $table->number }} â€” {{ $libre ? 'Libre' : ($isLista ? 'Listo' : 'Ocupada') }}">

                    <span id="mesa-dot-{{ $table->number }}"
                          class="mesa-dot"
                          style="background:{{ $dotColor }};{{ $libre ? 'animation:pulse 2s infinite;' : '' }}"></span>

                    <div class="mesa-num">{{ $table->number }}</div>
                    <div id="mesa-estado-{{ $table->number }}"
                         class="mesa-estado">{{ $libre ? 'libre' : ($isLista ? 'listo' : 'ocupada') }}</div>

                    <button id="btn-entregar-{{ $table->number }}"
                            class="btn-entregar {{ $isLista ? '' : 'btn-entregar-hidden' }}"
                            onclick="event.stopPropagation(); entregarPedido({{ $table->number }}, this)">
                        Pedido entregado
                    </button>
                </div>
            @endforeach
        </div>

        <p style="text-align:center; font-size:0.7rem; color:#374151; margin-top:1.5rem;">
            Click = abrir mesa &nbsp;&bull;&nbsp; Doble click = resumen rÃ¡pido
        </p>

    </div>

    <style>
        @media (min-width:480px)  { .mesas-grid { grid-template-columns: repeat(4,1fr)!important;  gap:1.25rem!important; } }
        @media (min-width:640px)  { .mesas-grid { grid-template-columns: repeat(5,1fr)!important;  gap:1.4rem!important; } }
        @media (min-width:768px)  { .mesas-grid { grid-template-columns: repeat(6,1fr)!important;  gap:1.5rem!important; } }
        @media (min-width:900px)  { .mesas-grid { grid-template-columns: repeat(7,1fr)!important;  gap:1.5rem!important; } }
        @media (min-width:1200px) { .mesas-grid { grid-template-columns: repeat(8,1fr)!important;  gap:1.5rem!important; } }
        @media (min-width:1440px) { .mesas-grid { grid-template-columns: repeat(9,1fr)!important;  gap:1.5rem!important; } }
        @media (min-width:1700px) { .mesas-grid { grid-template-columns: repeat(10,1fr)!important; gap:1.5rem!important; } }
        /* En mÃ³vil las mesas son mÃ¡s grandes y tÃ¡ctiles */
        @media (max-width:479px) {
            .mesa-num    { font-size: 2rem !important; }
            .mesa-estado { font-size: .65rem !important; }
            .mesa-btn    { padding: 1.2rem .5rem !important; min-height: 70px; }
        }
    </style>

    {{-- Modal resumen rÃ¡pido --}}
    <div id="modal-resumen"
         style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.75);
                z-index:100; align-items:center; justify-content:center; padding:1rem;"
         onclick="cerrarModal()">
        <div style="background:#161616; border:1px solid #333; border-radius:1rem;
                    box-shadow:0 25px 60px rgba(0,0,0,0.6); padding:1.5rem;
                    max-width:26rem; width:100%;"
             onclick="event.stopPropagation()">

            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                <h3 style="font-weight:800; font-size:1rem; color:#fbbf24;" id="modal-titulo">Resumen Mesa</h3>
                <button onclick="cerrarModal()"
                        style="color:#6b7280; background:none; border:none; font-size:1.25rem;
                               cursor:pointer; line-height:1; padding:0.25rem;"
                        onmouseover="this.style.color='#e5e7eb'"
                        onmouseout="this.style.color='#6b7280'">&#x2715;</button>
            </div>

            <div id="modal-contenido" style="font-size:0.82rem; color:#d1d5db;">
                <p style="color:#6b7280;">Cargando...</p>
            </div>

            <div style="margin-top:1.25rem; display:flex; gap:0.5rem; justify-content:flex-end;">
                <button onclick="cerrarModal()"
                        style="padding:0.5rem 1rem; font-size:0.78rem; color:#9ca3af;
                               border:1px solid #333; border-radius:0.5rem; background:#1a1a1a;
                               cursor:pointer;"
                        onmouseover="this.style.background='#222'"
                        onmouseout="this.style.background='#1a1a1a'">Cerrar</button>
                <button id="modal-btn-abrir"
                        style="padding:0.5rem 1rem; font-size:0.78rem; color:#fff;
                               background:#d97706; border:none; border-radius:0.5rem;
                               font-weight:700; cursor:pointer;"
                        onmouseover="this.style.background='#f59e0b'"
                        onmouseout="this.style.background='#d97706'">Abrir Mesa</button>
            </div>
        </div>
    </div>

    {{-- Inicializar TablesPage (logica en resources/js/tables.js, cargado via app.js) --}}
    <script>
        (function () {
            function _run() {
                if (window.TablesPage) { window.TablesPage.init(); return; }
                setTimeout(_run, 20);
            }
            _run();
        })();
    </script>
</x-app-layout>
