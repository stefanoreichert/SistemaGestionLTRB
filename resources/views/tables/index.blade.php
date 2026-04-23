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
            display: block;
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
        /* Amarillo: pedido listo en cocina, esperando entrega */
        .mesa-lista {
            background: rgba(120,83,0,0.35);
            border-color: rgba(234,179,8,0.55);
            color: #fef08a;
        }
        .mesa-lista:hover {
            background: rgba(120,83,0,0.55);
            border-color: #eab308;
            transform: scale(1.06);
            box-shadow: 0 0 18px rgba(234,179,8,0.35);
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
        /* Botón entregar dentro de la tarjeta */
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

        /* ── Delivery cards ──────────────────────────────── */
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
        <div id="mesas-grid" style="display:grid; grid-template-columns:repeat(3, 1fr); gap:1.5rem;"
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
                    title="Mesa {{ $table->number }} — {{ $libre ? 'Libre' : ($isLista ? 'Listo' : 'Ocupada') }}">

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
            Click = abrir mesa &nbsp;&bull;&nbsp; Doble click = resumen rápido
        </p>

        {{-- Sección Deliveries --}}
        <div id="deliveries-section" style="margin-top:1.75rem;">
            <h2 style="font-size:.78rem; font-weight:700; color:#a78bfa; text-transform:uppercase;
                       letter-spacing:.1em; margin-bottom:.75rem;">
                🛵 Deliveries activos
                <span id="delivery-count" style="margin-left:.5rem; font-size:.7rem;
                      background:rgba(139,92,246,.25); color:#c4b5fd; padding:.1rem .5rem;
                      border-radius:1rem;">0</span>
            </h2>
            <div id="deliveries-list"
                 style="display:grid; grid-template-columns:repeat(auto-fill,minmax(130px,1fr)); gap:.6rem;">
                <p id="deliveries-empty"
                   style="color:#4b5563; font-style:italic; font-size:.8rem; grid-column:1/-1;">
                    Sin deliveries activos.
                </p>
            </div>
        </div>
    </div>

    <style>
        @media (min-width:480px)  { .mesas-grid { grid-template-columns: repeat(5,1fr)!important;  gap:1.5rem!important; } }
        @media (min-width:640px)  { .mesas-grid { grid-template-columns: repeat(6,1fr)!important;  gap:1.5rem!important; } }
        @media (min-width:768px)  { .mesas-grid { grid-template-columns: repeat(7,1fr)!important;  gap:1.6rem!important; } }
        @media (min-width:900px)  { .mesas-grid { grid-template-columns: repeat(8,1fr)!important;  gap:1.75rem!important; } }
        @media (min-width:1200px) { .mesas-grid { grid-template-columns: repeat(9,1fr)!important;  gap:2rem!important; } }
        @media (min-width:1440px) { .mesas-grid { grid-template-columns: repeat(10,1fr)!important; gap:2rem!important; } }
        /* En móvil las mesas son más grandes y táctiles */
        @media (max-width:479px) {
            .mesa-num    { font-size: 2rem !important; }
            .mesa-estado { font-size: .65rem !important; }
            .mesa-btn    { padding: 1rem .5rem !important; min-height: 70px; }
        }
    </style>

    {{-- Modal resumen rápido --}}
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
            const btn    = document.getElementById('mesa-btn-' + num);
            const dot    = document.getElementById('mesa-dot-' + num);
            const label  = document.getElementById('mesa-estado-' + num);
            const btnEnt = document.getElementById('btn-entregar-' + num);
            if (!btn) return;
            btn.className        = 'mesa-btn mesa-libre';
            btn.title            = `Mesa ${num} — Libre`;
            btn.dataset.kitchenStatus = 'pendiente';
            dot.style.background = '#10b981';
            dot.style.animation  = 'pulse 2s infinite';
            label.textContent    = 'libre';
            if (btnEnt) btnEnt.classList.add('btn-entregar-hidden');
            recalcContadores();
        }

        function setMesaOcupada(num) {
            const btn    = document.getElementById('mesa-btn-' + num);
            const dot    = document.getElementById('mesa-dot-' + num);
            const label  = document.getElementById('mesa-estado-' + num);
            const btnEnt = document.getElementById('btn-entregar-' + num);
            if (!btn) return;
            btn.className        = 'mesa-btn mesa-ocupada';
            btn.title            = `Mesa ${num} — Ocupada`;
            btn.dataset.kitchenStatus = 'pendiente';
            dot.style.background = '#ef4444';
            dot.style.animation  = '';
            label.textContent    = 'ocupada';
            if (btnEnt) btnEnt.classList.add('btn-entregar-hidden');
            recalcContadores();
        }

        function setMesaLista(num) {
            const btn    = document.getElementById('mesa-btn-' + num);
            const dot    = document.getElementById('mesa-dot-' + num);
            const label  = document.getElementById('mesa-estado-' + num);
            const btnEnt = document.getElementById('btn-entregar-' + num);
            if (!btn) return;
            btn.className        = 'mesa-btn mesa-lista';
            btn.title            = `Mesa ${num} — Listo para entregar`;
            btn.dataset.kitchenStatus = 'listo';
            dot.style.background = '#eab308';
            dot.style.animation  = '';
            label.textContent    = 'listo';
            if (btnEnt) btnEnt.classList.remove('btn-entregar-hidden');
            recalcContadores();
        }

        function recalcContadores() {
            const total    = document.querySelectorAll('.mesa-btn').length;
            const occupied = document.querySelectorAll('.mesa-ocupada, .mesa-lista').length;
            const free     = total - occupied;
            document.getElementById('lbl-free').textContent     = `${free} libres`;
            document.getElementById('lbl-occupied').textContent = `${occupied} ocupadas`;
        }

        /* ── Entregar pedido ────────────────────────────────── */
        function entregarPedido(num, btn) {
            const card    = document.getElementById('mesa-btn-' + num);
            const orderId = card?.dataset.orderId;
            if (!orderId) return;

            btn.disabled    = true;
            btn.textContent = '…';

            fetch(`/orders/${orderId}/deliver`, {
                method:  'PATCH',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    // Vuelve a rojo: pedido entregado, sigue abierto hasta cobrar
                    setMesaOcupada(num);
                } else {
                    btn.disabled    = false;
                    btn.textContent = 'Pedido entregado';
                }
            })
            .catch(() => {
                btn.disabled    = false;
                btn.textContent = 'Pedido entregado';
            });
        }

        /* ── Polling de kitchen_status (cada 5 s) ───────────── */
        function pollKitchenStatus() {
            fetch('/api/tables/statuses', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
            })
            .then(r => r.json())
            .then(rows => {
                rows.forEach(row => {
                    const num  = row.number;
                    const card = document.getElementById('mesa-btn-' + num);
                    if (!card) return;

                    // Actualizar order_id en data attribute
                    if (row.order_id) card.dataset.orderId = row.order_id;

                    const current = card.dataset.kitchenStatus ?? 'pendiente';
                    const next    = row.kitchen_status;

                    if (!row.has_order) {
                        if (!card.classList.contains('mesa-libre')) setMesaLibre(num);
                        return;
                    }
                    if (next === 'listo' && current !== 'listo') { setMesaLista(num); return; }
                    if (next !== 'listo' && current === 'listo') { setMesaOcupada(num); return; }
                });
            })
            .catch(() => {/* silencioso */});
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

        /* ── Delivery: lista ────────────────────────────────── */
        function escapeHtmlDel(str) {
            return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;')
                .replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
        }

        function abrirDelivery(orderId) {
            window.location.href = '/delivery/' + orderId;
        }

        async function entregarDelivery(orderId, btn) {
            btn.disabled    = true;
            btn.textContent = '…';
            try {
                const r = await fetch('/delivery/' + orderId + '/deliver', {
                    method: 'PATCH',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                });
                const data = await r.json();
                if (data.success) {
                    document.getElementById('del-card-' + orderId)?.remove();
                    updateDeliveryCount();
                } else {
                    btn.disabled    = false;
                    btn.textContent = 'Entregar';
                }
            } catch {
                btn.disabled    = false;
                btn.textContent = 'Entregar';
            }
        }

        function renderDeliveryCard(d) {
            const isListo = d.kitchen_status === 'listo';
            const card = document.createElement('div');
            card.id          = 'del-card-' + d.order_id;
            card.className   = 'delivery-card';
            card.title       = 'Abrir delivery: ' + escapeHtmlDel(d.delivery_label);
            card.onclick     = () => abrirDelivery(d.order_id);
            card.innerHTML   = `
                <div class="delivery-label">\ud83d\udef5 ${escapeHtmlDel(d.delivery_label)}</div>
                <div class="delivery-meta">${d.items_count} item${d.items_count !== 1 ? 's' : ''} &bull; ${d.opened_time || ''}</div>
                <button id="del-ent-${d.order_id}"
                        class="btn-del-entregar ${isListo ? '' : 'btn-del-entregar-hidden'}"
                        onclick="event.stopPropagation(); entregarDelivery(${d.order_id}, this)">
                    Entregar
                </button>`;
            return card;
        }

        function updateDeliveryCount() {
            const count = document.querySelectorAll('.delivery-card').length;
            document.getElementById('delivery-count').textContent = count;
            document.getElementById('deliveries-empty').style.display = count ? 'none' : '';
        }

        function handleDeliveryUpdate(data) {
            if (data.action === 'entregado' || data.action === 'closed' || data.action === 'cancelled') {
                document.getElementById('del-card-' + data.order_id)?.remove();
                updateDeliveryCount();
                return;
            }
            // updated → actualizar o insertar tarjeta
            const existing = document.getElementById('del-card-' + data.order_id);
            const isListo  = data.kitchen_status === 'listo';
            if (existing) {
                const btnEnt = document.getElementById('del-ent-' + data.order_id);
                if (btnEnt) {
                    if (isListo) btnEnt.classList.remove('btn-del-entregar-hidden');
                    else         btnEnt.classList.add('btn-del-entregar-hidden');
                }
            } else {
                const newCard = renderDeliveryCard({
                    order_id:       data.order_id,
                    delivery_label: data.delivery_label,
                    kitchen_status: data.kitchen_status ?? 'pendiente',
                    items_count:    data.items_count ?? 0,
                    opened_time:    data.opened_at ? new Date(data.opened_at).toLocaleTimeString('es-AR',{hour:'2-digit',minute:'2-digit'}) : '',
                });
                const list = document.getElementById('deliveries-list');
                if (list) list.appendChild(newCard);
            }
            updateDeliveryCount();
        }

        async function loadDeliveries() {
            try {
                const r    = await fetch('/api/delivery', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } });
                const list = await r.json();
                const container = document.getElementById('deliveries-list');
                // Limpiar tarjetas existentes (conservar el párrafo vacío)
                container.querySelectorAll('.delivery-card').forEach(c => c.remove());
                list.forEach(d => container.appendChild(renderDeliveryCard(d)));
                updateDeliveryCount();
            } catch { /* silencioso */ }
        }

        document.addEventListener('keydown', e => { if (e.key === 'Escape') { cerrarModal(); } });

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
                    if (data.is_delivery) {
                        handleDeliveryUpdate(data);
                        return;
                    }
                    const tableNum = data.table_number;
                    if (!tableNum) return;
                    if (data.action === 'updated') {
                        setMesaOcupada(tableNum);
                    } else if (data.action === 'closed' || data.action === 'cancelled') {
                        setMesaLibre(tableNum);
                    }
                });
        }

        function __tablesInit() {
            initEcho();
            // Guardar IDs de intervalos para poder limpiarlos en SPA
            window._tablesIntervals = window._tablesIntervals || [];
            window._tablesIntervals.push(setInterval(pollKitchenStatus, 5000));
            loadDeliveries();
            window._tablesIntervals.push(setInterval(loadDeliveries, 10000));

            // Registrar limpieza para navegación SPA
            if (typeof window.spaRegisterCleanup === 'function') {
                window.spaRegisterCleanup(function () {
                    (window._tablesIntervals || []).forEach(clearInterval);
                    window._tablesIntervals = [];
                    if (window.Echo) { try { window.Echo.leave('restaurant'); } catch (e) {} }
                });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', __tablesInit);
        } else {
            __tablesInit();
        }
    </script>
</x-app-layout>