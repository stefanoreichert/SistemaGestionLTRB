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
                <span style="font-size:0.7rem; color:#4b5563;" id="ws-label">Conectando…</span>
            </div>

        </div>
    </x-slot>

    <style>
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
        .mesa-btn {
            position: relative;
            border-radius: 0.875rem;
            border-width: 2px;
            border-style: solid;
            padding: 0.875rem 0.5rem;
            text-align: center;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s, border-color 0.15s;
            user-select: none;
            background: none;
            width: 100%;
        }
        .mesa-btn:active { transform: scale(0.93) !important; }
        .mesa-libre {
            background: rgba(5,78,40,0.35);
            border-color: rgba(16,185,129,0.35);
            color: #6ee7b7;
        }
        .mesa-libre:hover {
            background: rgba(5,78,40,0.6);
            border-color: #10b981;
            transform: scale(1.06);
            box-shadow: 0 0 18px rgba(16,185,129,0.25);
        }
        .mesa-ocupada {
            background: rgba(69,10,10,0.45);
            border-color: rgba(239,68,68,0.35);
            color: #fca5a5;
        }
        .mesa-ocupada:hover {
            background: rgba(69,10,10,0.7);
            border-color: #ef4444;
            transform: scale(1.06);
            box-shadow: 0 0 18px rgba(239,68,68,0.25);
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
    </style>

    <div style="padding:1.25rem;">

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
        <div id="mesas-grid" style="display:grid; grid-template-columns:repeat(5, 1fr); gap:0.6rem;"
             class="mesas-grid">
            @foreach($tables as $table)
                @php $libre = $table->isFree(); @endphp
                <button
                    id="mesa-btn-{{ $table->number }}"
                    class="mesa-btn {{ $libre ? 'mesa-libre' : 'mesa-ocupada' }}"
                    onclick="abrirMesa({{ $table->number }})"
                    ondblclick="verResumen({{ $table->number }}, event)"
                    title="Mesa {{ $table->number }} — {{ $libre ? 'Libre' : 'Ocupada' }}">

                    <span id="mesa-dot-{{ $table->number }}"
                          class="mesa-dot"
                          style="background: {{ $libre ? '#10b981' : '#ef4444' }};{{ $libre ? 'animation:pulse 2s infinite;' : '' }}"></span>

                    <div class="mesa-num">{{ $table->number }}</div>
                    <div id="mesa-estado-{{ $table->number }}"
                         class="mesa-estado">{{ $libre ? 'libre' : 'ocupada' }}</div>
                </button>
            @endforeach
        </div>

        <p style="text-align:center; font-size:0.7rem; color:#374151; margin-top:1.5rem;">
            Click = abrir mesa &nbsp;&bull;&nbsp; Doble click = resumen rápido
        </p>
    </div>

    <style>
        @media (min-width:480px)  { .mesas-grid { grid-template-columns: repeat(6,1fr)!important; } }
        @media (min-width:640px)  { .mesas-grid { grid-template-columns: repeat(8,1fr)!important; } }
        @media (min-width:900px)  { .mesas-grid { grid-template-columns: repeat(10,1fr)!important; } }
    </style>

    {{-- Modal de resumen rápido --}}
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

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        /* ── Navegación ─────────────────────────────────────── */
        function abrirMesa(numero) {
            window.location.href = `/tables/${numero}/order`;
        }

        /* ── Helpers de estado ──────────────────────────────── */
        function setMesaLibre(num) {
            const btn   = document.getElementById('mesa-btn-' + num);
            const dot   = document.getElementById('mesa-dot-' + num);
            const label = document.getElementById('mesa-estado-' + num);
            if (!btn) return;
            btn.className   = 'mesa-btn mesa-libre';
            btn.title       = `Mesa ${num} — Libre`;
            dot.style.background  = '#10b981';
            dot.style.animation   = 'pulse 2s infinite';
            label.textContent     = 'libre';
            recalcContadores();
        }

        function setMesaOcupada(num) {
            const btn   = document.getElementById('mesa-btn-' + num);
            const dot   = document.getElementById('mesa-dot-' + num);
            const label = document.getElementById('mesa-estado-' + num);
            if (!btn) return;
            btn.className   = 'mesa-btn mesa-ocupada';
            btn.title       = `Mesa ${num} — Ocupada`;
            dot.style.background  = '#ef4444';
            dot.style.animation   = '';
            label.textContent     = 'ocupada';
            recalcContadores();
        }

        function recalcContadores() {
            const total    = document.querySelectorAll('.mesa-btn').length;
            const occupied = document.querySelectorAll('.mesa-ocupada').length;
            const free     = total - occupied;
            document.getElementById('lbl-free').textContent     = `${free} libres`;
            document.getElementById('lbl-occupied').textContent = `${occupied} ocupadas`;
        }

        /* ── Modal de resumen ───────────────────────────────── */
        function verResumen(numero, e) {
            e.preventDefault();
            e.stopPropagation();
            document.getElementById('modal-titulo').textContent = `Mesa ${numero}`;
            document.getElementById('modal-contenido').innerHTML = '<p style="color:#6b7280;">Cargando...</p>';
            document.getElementById('modal-btn-abrir').onclick = () => abrirMesa(numero);
            document.getElementById('modal-resumen').style.display = 'flex';

            fetch(`/api/tables/${numero}/summary`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    document.getElementById('modal-contenido').innerHTML =
                        `<p style="color:#6b7280; font-style:italic;">${data.message}</p>`;
                    return;
                }
                let html = `<table style="width:100%; border-collapse:collapse; margin-bottom:0.75rem;">
                    <thead><tr style="border-bottom:1px solid #333;">
                        <th style="text-align:left; padding-bottom:0.5rem; color:#6b7280; font-weight:500; font-size:0.75rem;">Producto</th>
                        <th style="text-align:center; padding-bottom:0.5rem; color:#6b7280; font-weight:500; font-size:0.75rem;">Cant.</th>
                        <th style="text-align:right; padding-bottom:0.5rem; color:#6b7280; font-weight:500; font-size:0.75rem;">Subtotal</th>
                    </tr></thead><tbody>`;
                data.items.forEach(item => {
                    html += `<tr style="border-bottom:1px solid #1f1f1f;">
                        <td style="padding:0.4rem 0; color:#e5e7eb;">${item.name}</td>
                        <td style="padding:0.4rem 0; text-align:center; color:#9ca3af;">${item.quantity}</td>
                        <td style="padding:0.4rem 0; text-align:right; color:#4ade80;">$${fmt(item.subtotal)}</td>
                    </tr>`;
                });
                html += `</tbody></table>
                    <div style="text-align:right; font-weight:800; color:#fbbf24; font-size:1rem;">
                        Total: $${fmt(data.total)}
                    </div>`;
                document.getElementById('modal-contenido').innerHTML = html;
            })
            .catch(() => {
                document.getElementById('modal-contenido').innerHTML =
                    '<p style="color:#f87171;">Error al cargar el resumen.</p>';
            });
        }

        function cerrarModal() {
            document.getElementById('modal-resumen').style.display = 'none';
        }

        function fmt(n) { return parseFloat(n).toLocaleString('es-AR'); }

        document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarModal(); });

        /* ── WebSocket: tiempo real ─────────────────────────── */
        function initEcho() {
            if (typeof window.Echo === 'undefined') {
                setTimeout(initEcho, 500);
                return;
            }

            const indicator = document.getElementById('ws-indicator');
            const wsLabel   = document.getElementById('ws-label');

            window.Echo.connector.pusher.connection.bind('connected', () => {
                indicator.style.background = '#10b981';
                wsLabel.textContent = 'En vivo';
                wsLabel.style.color = '#34d399';
            });
            window.Echo.connector.pusher.connection.bind('disconnected', () => {
                indicator.style.background = '#ef4444';
                wsLabel.textContent = 'Desconectado';
                wsLabel.style.color = '#f87171';
            });
            window.Echo.connector.pusher.connection.bind('connecting', () => {
                indicator.style.background = '#d97706';
                wsLabel.textContent = 'Conectando…';
                wsLabel.style.color = '#fbbf24';
            });

            window.Echo.channel('restaurant')
                .listen('.order.updated', (data) => {
                    const tableNum = data.table_number;
                    if (!tableNum) return;

                    if (data.action === 'updated') {
                        setMesaOcupada(tableNum);
                    } else if (data.action === 'closed' || data.action === 'cancelled') {
                        setMesaLibre(tableNum);
                    }
                });
        }

        document.addEventListener('DOMContentLoaded', initEcho);
    </script>
</x-app-layout>