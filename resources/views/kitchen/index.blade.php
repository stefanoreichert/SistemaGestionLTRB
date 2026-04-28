<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cocina — Los Troncos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #0a0a0a; color: #e5e7eb; font-family: system-ui, sans-serif; }

        /* ── Header ──────────────────────────────────────────── */
        .kds-header {
            background: #111; border-bottom: 1px solid #1f1f1f;
            padding: 0.65rem 1.25rem; display: flex; align-items: center;
            justify-content: space-between; gap: 1rem;
            position: sticky; top: 0; z-index: 20;
        }
        .kds-title { font-size: 1rem; font-weight: 800; color: #fbbf24; letter-spacing: .04em; }
        .kds-badge { padding: 0.2rem 0.6rem; border-radius: 999px; font-weight: 700; font-size: 0.7rem; }
        .badge-open   { background: rgba(251,191,36,.15); color: #fbbf24; border: 1px solid rgba(251,191,36,.3); }
        .badge-ws-on  { background: rgba(16,185,129,.15); color: #34d399; border: 1px solid rgba(16,185,129,.3); }
        .badge-ws-off { background: rgba(239,68,68,.15);  color: #f87171; border: 1px solid rgba(239,68,68,.3); }
        .kds-nav-btn {
            font-size: 0.75rem; padding: 0.3rem 0.75rem; border-radius: 0.4rem;
            background: #1a1a1a; border: 1px solid #2a2a2a; color: #9ca3af;
            cursor: pointer; text-decoration: none; display: inline-block;
        }
        .kds-nav-btn:hover { background: #222; color: #e5e7eb; }

        /* ── Grilla ──────────────────────────────────────────── */
        .kds-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
            gap: 0.75rem; padding: 0.85rem 1rem;
        }

        /* ── Tarjeta ─────────────────────────────────────────── */
        .order-card {
            background: #161616; border: 1px solid #2a2a2a;
            border-radius: 0.875rem; overflow: hidden;
            display: flex; flex-direction: column;
            transition: border-color 0.3s;
        }
        .order-card.urgent  { border-color: #7f1d1d; }
        .order-card.warning { border-color: #78350f; }
        .order-card.card-listo {
            border-color: rgba(234,179,8,.55) !important;
            background: rgba(234,179,8,.04);
        }

        /* ── Encabezado de tarjeta ───────────────────────────── */
        .card-header {
            padding: 0.65rem 0.85rem; display: flex;
            align-items: center; justify-content: space-between;
            border-bottom: 1px solid #1f1f1f;
        }
        .card-mesa { font-size: 1.3rem; font-weight: 900; color: #fbbf24; }
        .card-time {
            font-size: 0.68rem; font-weight: 600; padding: 0.2rem 0.5rem;
            border-radius: 999px; border: 1px solid;
        }
        .time-ok      { color: #4ade80; border-color: rgba(74,222,128,.3); background: rgba(74,222,128,.08); }
        .time-warning { color: #fbbf24; border-color: rgba(251,191,36,.3); background: rgba(251,191,36,.08); }
        .time-urgent  { color: #f87171; border-color: rgba(248,113,113,.3); background: rgba(248,113,113,.08);
                        animation: pulse-txt 1.5s ease-in-out infinite; }
        @keyframes pulse-txt { 0%,100%{opacity:1} 50%{opacity:.55} }

        /* ── Ítems ───────────────────────────────────────────── */
        .card-items { padding: 0.5rem 0.6rem; flex: 1; }
        .item-row {
            display: flex; align-items: flex-start; gap: 0.5rem;
            padding: 0.35rem 0.4rem; border-radius: 0.4rem; margin-bottom: 0.25rem;
        }
        .item-qty  { color: #9ca3af; font-size: 0.75rem; font-weight: 700; min-width: 1.8rem; text-align: right; padding-top: 0.05rem; }
        .item-info { flex: 1; display: flex; flex-direction: column; gap: 0.1rem; }
        .item-name { font-size: 0.78rem; color: #e5e7eb; }
        .item-note { font-size: 0.68rem; color: #f59e0b; font-style: italic; opacity: 0.9; }
        .item-row.item-done .item-name { text-decoration: line-through; opacity: 0.4; }
        .item-row.item-done .item-note { opacity: 0.3; }

        /* Botones por ítem: ciclo NUEVO → PREP. → LISTO */
        .item-status-btn {
            font-size: 0.6rem; font-weight: 700; padding: 0.18rem 0.45rem;
            border-radius: 999px; border: 1px solid; cursor: pointer;
            white-space: nowrap; letter-spacing: .03em; background: none;
        }
        .s-new            { color: #60a5fa; border-color: rgba(96,165,250,.4); }
        .s-new:hover      { background: rgba(96,165,250,.12); }
        .s-preparing      { color: #fb923c; border-color: rgba(251,146,60,.4); }
        .s-preparing:hover{ background: rgba(251,146,60,.12); }
        .s-ready          { color: #4ade80; border-color: rgba(74,222,128,.4); }
        .s-ready:hover    { background: rgba(74,222,128,.12); }

        /* ── Pie de tarjeta ──────────────────────────────────── */
        .card-footer {
            padding: 0.45rem 0.85rem; border-top: 1px solid #1f1f1f;
            font-size: 0.68rem; color: #4b5563;
            display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;
        }

        /* Botón toggle de estado de pedido (orden completa) */
        .btn-order-status {
            flex-shrink: 0; font-size: 0.6rem; font-weight: 700;
            padding: 0.18rem 0.55rem; border-radius: 999px;
            cursor: pointer; white-space: nowrap; letter-spacing: .03em;
            background: none; transition: background .15s;
        }
        .btn-en-proceso {
            color: #fbbf24; border: 1px solid rgba(251,191,36,.4);
        }
        .btn-en-proceso:hover { background: rgba(251,191,36,.12); }
        .btn-listo {
            color: #4ade80; border: 1px solid rgba(74,222,128,.4);
        }
        .btn-listo:disabled { opacity: 0.75; cursor: default; }

        /* ── Vacío ───────────────────────────────────────────── */
        .kds-empty {
            grid-column: 1 / -1; text-align: center;
            padding: 4rem 1rem; color: #374151;
        }
        .kds-empty span { font-size: 3rem; display: block; margin-bottom: 0.75rem; }
        /* ── Delivery en KDS ─────────────────────────── */
        .delivery-card { border-color: rgba(139,92,246,.5) !important; background: rgba(60,20,120,.25); }
        .delivery-mesa { color: #a78bfa; }

        /* ── Sección Entregados ───────────────────────── */
        #entregados-section {
            padding: .75rem 1rem 1.5rem;
            border-top: 1px solid #1f1f1f;
            display: none;
        }
        .entregados-title {
            font-size:.72rem; font-weight:700; color:#4b5563;
            text-transform:uppercase; letter-spacing:.1em;
            margin-bottom:.65rem;
        }
        .entregados-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:.5rem; }
        .entregado-card {
            background:rgba(5,78,40,.2); border:1px solid rgba(16,185,129,.25);
            border-radius:.65rem; padding:.6rem .8rem; opacity:.75;
            display:flex; align-items:center; justify-content:space-between; gap:.5rem;
        }
        .entregado-label { font-size:.8rem; font-weight:700; color:#6ee7b7; }
        .entregado-time  { font-size:.65rem; color:#4b5563; }    </style>
</head>
<body>

<header class="kds-header">
    <div style="display:flex;align-items:center;gap:0.75rem;">
        <span class="kds-title">👨‍🍳 Cocina — Los Troncos</span>
        <span class="kds-badge badge-open" id="lbl-mesas">
            {{ $orders->count() }} {{ $orders->count() === 1 ? 'mesa' : 'mesas' }}
        </span>
    </div>
    <div style="display:flex;align-items:center;gap:0.5rem;">
        <span class="kds-badge badge-ws-off" id="ws-badge">Conectando…</span>
        <a href="/tables" class="kds-nav-btn">🪑 Mesas</a>
        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
            @csrf
            <button type="submit" class="kds-nav-btn" style="border-color:#4b1c1c;color:#f87171;">⏏ Salir</button>
        </form>
    </div>
</header>

<main id="kds-grid" class="kds-grid">

@forelse($orders as $order)
    @php
        $mins    = $order->opened_at ? (int) $order->opened_at->diffInMinutes(now()) : 0;
        $tClass  = $mins >= 30 ? 'time-urgent' : ($mins >= 15 ? 'time-warning' : 'time-ok');
        $cClass  = $mins >= 30 ? 'urgent' : ($mins >= 15 ? 'warning' : '');
        $ks      = $order->kitchen_status ?? 'pendiente';
        $isListo = $ks === 'listo';
    @endphp

    <div class="order-card {{ $cClass }} {{ $isListo ? 'card-listo' : '' }} {{ $order->is_delivery ? 'delivery-card' : '' }}"
         id="card-order-{{ $order->id }}"
         data-table="{{ $order->is_delivery ? 0 : $order->table->number }}"
         data-kitchen-status="{{ $ks }}"
         data-opened-at="{{ $order->opened_at?->toIso8601String() }}">

        <div class="card-header">
            <div class="card-mesa {{ $order->is_delivery ? 'delivery-mesa' : '' }}">
                @if($order->is_delivery)
                    🛵 {{ $order->delivery_label }}
                @else
                    Mesa {{ $order->table->number }}
                @endif
            </div>
            <div class="card-time {{ $tClass }}" id="time-{{ $order->id }}">{{ $mins }}m</div>
        </div>

        <div class="card-items" id="items-{{ $order->id }}">
            @foreach($order->items as $item)
                @php $itemKs = $item->kitchen_status ?? 'new'; @endphp
                <div class="item-row {{ $itemKs === 'ready' ? 'item-done' : '' }}"
                     id="item-row-{{ $item->id }}">
                    <span class="item-qty">{{ $item->quantity }}×</span>
                    <div class="item-info">
                        <span class="item-name">{{ $item->product->name }}</span>
                        @if(!empty($item->notes))
                            <span class="item-note">⚠ {{ $item->notes }}</span>
                        @endif
                    </div>
                    <button class="item-status-btn s-{{ $itemKs }}"
                            id="btn-{{ $item->id }}"
                            data-status="{{ $itemKs }}"
                            onclick="cycleStatus({{ $item->id }}, {{ $order->id }}, {{ $order->is_delivery ? 0 : $order->table->number }})">
                        {{ $itemKs === 'new' ? 'NUEVO' : ($itemKs === 'preparing' ? 'PREP.' : 'LISTO') }}
                    </button>
                </div>
            @endforeach
        </div>

        <div class="card-footer">
            <span id="footer-count-{{ $order->id }}">
                {{ $order->items->count() }} ítem(s) &nbsp;·&nbsp; {{ $order->opened_at ? $order->opened_at->format('H:i') : '—' }}
            </span>

            @if($isListo)
                <button class="btn-order-status btn-listo"
                        id="ks-btn-{{ $order->id }}"
                        disabled>
                    ✓ Pedido listo
                </button>
            @else
                <button class="btn-order-status btn-en-proceso"
                        id="ks-btn-{{ $order->id }}"
                        onclick="toggleOrderStatus({{ $order->id }}, this)">
                    Marcar como listo
                </button>
            @endif
        </div>
    </div>
@empty
    <div class="kds-empty" id="kds-empty-msg">
        <span>🎉</span>
        <p style="font-size:1rem;font-weight:700;color:#6b7280;">Sin pedidos activos</p>
        <p style="font-size:0.8rem;margin-top:0.25rem;">La cocina está al día.</p>
    </div>
@endforelse

</main>

<section id="entregados-section">
    <div class="entregados-title">
        ✓ Entregados recientemente
        <button onclick="limpiarEntregados()"
                style="margin-left:.75rem;font-size:.65rem;color:#6b7280;background:none;
                       border:1px solid #333;border-radius:.35rem;padding:.15rem .45rem;
                       cursor:pointer;"
                onmouseover="this.style.color='#9ca3af'"
                onmouseout="this.style.color='#6b7280'">Limpiar</button>
    </div>
    <div class="entregados-grid" id="entregados-list"></div>
</section>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const STATUS_CYCLE = { new:'preparing', preparing:'ready', ready:'new' };
const STATUS_LABEL = { new:'NUEVO', preparing:'PREP.', ready:'LISTO' };

/* ── Solo ítems de comida (filtro WebSocket) ─────── */
function soloComida(items) {
    return (items || []).filter(i => i.type === 'Comida');
}

/* ── Ciclar estado ítem (NUEVO → PREP. → LISTO) ─── */
function cycleStatus(itemId, orderId, tableNum) {
    const btn = document.getElementById('btn-' + itemId);
    if (!btn) return;
    const current = btn.dataset.status;
    const next    = STATUS_CYCLE[current] ?? 'new';
    applyItemStatus(itemId, next); // optimista

    fetch(`/kitchen/items/${itemId}/status`, {
        method:  'PUT',
        headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': CSRF },
        body:    JSON.stringify({ status: next }),
    })
    .then(r => r.json())
    .then(d => { if (!d.success) applyItemStatus(itemId, current); })
    .catch(()  => applyItemStatus(itemId, current));
}

function applyItemStatus(itemId, status) {
    const btn = document.getElementById('btn-'      + itemId);
    const row = document.getElementById('item-row-' + itemId);
    if (!btn || !row) return;
    btn.dataset.status = status;
    btn.textContent    = STATUS_LABEL[status] ?? status;
    btn.className      = 'item-status-btn s-' + status;
    row.className      = 'item-row' + (status === 'ready' ? ' item-done' : '');
}

/* ── Toggle estado pedido (orden completa) ───────── */
function toggleOrderStatus(orderId, btn) {
    btn.disabled    = true;
    btn.textContent = '…';

    fetch(`/kitchen/orders/${orderId}/status`, {
        method:  'PATCH',
        headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': CSRF },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            applyOrderStatus(orderId, data.kitchen_status);
        } else {
            btn.disabled    = false;
            btn.textContent = 'Marcar como listo';
        }
    })
    .catch(() => {
        btn.disabled    = false;
        btn.textContent = 'Marcar como listo';
    });
}

function applyOrderStatus(orderId, ks) {
    const card = document.getElementById('card-order-' + orderId);
    const btn  = document.getElementById('ks-btn-'     + orderId);
    if (!card || !btn) return;

    card.dataset.kitchenStatus = ks;

    if (ks === 'listo') {
        card.classList.add('card-listo');
        btn.disabled    = true;
        btn.textContent = '✓ Pedido listo';
        btn.className   = 'btn-order-status btn-listo';
        btn.removeAttribute('onclick');
    } else {
        card.classList.remove('card-listo');
        btn.disabled    = false;
        btn.textContent = 'Marcar como listo';
        btn.className   = 'btn-order-status btn-en-proceso';
        btn.setAttribute('onclick', `toggleOrderStatus(${orderId}, this)`);
    }
}

/* ── Toast de notificación ──────────────────────── */
function showToast(msg) {
    const t = document.createElement('div');
    t.textContent = msg;
    t.style.cssText = [
        'position:fixed', 'bottom:1.5rem', 'right:1.5rem', 'z-index:999',
        'background:#065f46', 'color:#6ee7b7',
        'border:1px solid rgba(52,211,153,.4)',
        'padding:.65rem 1.1rem', 'border-radius:.6rem',
        'font-size:.82rem', 'font-weight:700',
        'box-shadow:0 4px 20px rgba(0,0,0,.5)',
        'transition:opacity .4s', 'opacity:1',
    ].join(';');
    document.body.appendChild(t);
    setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 420); }, 3000);
}

/* ── Escape XSS para texto dinámico ──────────────── */
function escapeHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

/* ── Render tarjeta nueva (WebSocket) ────────────── */
function renderCard(data) {
    const comidaItems = soloComida(data.items);
    if (!comidaItems.length) return null; // No mostrar tarjeta sin ítems de comida

    const mins = data.opened_at
        ? Math.floor((Date.now() - new Date(data.opened_at).getTime()) / 60000) : 0;
    const tC = mins >= 30 ? 'time-urgent' : (mins >= 15 ? 'time-warning' : 'time-ok');
    const cC = mins >= 30 ? 'urgent'      : (mins >= 15 ? 'warning'      : '');

    const isDelivery  = !!data.is_delivery;
    const mesaLabel   = isDelivery
        ? `🛵 ${escapeHtml(data.delivery_label || '')}`
        : `Mesa ${data.table_number}`;
    const mesaClass   = isDelivery ? 'card-mesa delivery-mesa' : 'card-mesa';
    const cardExtra   = isDelivery ? ' delivery-card' : '';

    const items = comidaItems.map(i => `
        <div class="item-row" id="item-row-${i.id}">
            <span class="item-qty">${i.quantity}×</span>
            <div class="item-info">
                <span class="item-name">${escapeHtml(i.name)}</span>
                ${i.notes ? `<span class="item-note">⚠ ${escapeHtml(i.notes)}</span>` : ''}
            </div>
            <button class="item-status-btn s-new" id="btn-${i.id}" data-status="new"
                    onclick="cycleStatus(${i.id},${data.order_id},${data.table_number ?? 0})">NUEVO</button>
        </div>`).join('');
    const openedTime = data.opened_at
        ? new Date(data.opened_at).toLocaleTimeString('es-AR', { hour:'2-digit', minute:'2-digit' }) : '—';

    return `<div class="order-card ${cC}${cardExtra}" id="card-order-${data.order_id}"
                 data-table="${data.table_number ?? 0}" data-kitchen-status="pendiente"
                 data-opened-at="${data.opened_at || ''}">
        <div class="card-header">
            <div class="${mesaClass}">${mesaLabel}</div>
            <div class="card-time ${tC}" id="time-${data.order_id}">${mins}m</div>
        </div>
        <div class="card-items" id="items-${data.order_id}">${items}</div>
        <div class="card-footer">
            <span id="footer-count-${data.order_id}">${comidaItems.length} ítem(s) &nbsp;·&nbsp; ${openedTime}</span>
            <button class="btn-order-status btn-en-proceso" id="ks-btn-${data.order_id}"
                    onclick="toggleOrderStatus(${data.order_id}, this)">Marcar como listo</button>
        </div>
    </div>`;
}

/* ── Sync ítems en tarjeta existente ─────────────── */
function syncItems(data) {
    const container = document.getElementById('items-' + data.order_id);
    if (!container) return;

    // Solo ítems de comida en el KDS
    const comidaItems = soloComida(data.items);

    const existing = new Set([...container.querySelectorAll('[id^="item-row-"]')]
        .map(el => parseInt(el.id.replace('item-row-', ''))));
    const incoming = new Set(comidaItems.map(i => i.id));

    // Agregar nuevos ítems de comida
    comidaItems.forEach(i => {
        if (!existing.has(i.id)) {
            container.insertAdjacentHTML('beforeend', `
                <div class="item-row" id="item-row-${i.id}">
                    <span class="item-qty">${i.quantity}×</span>
                    <div class="item-info">
                        <span class="item-name">${escapeHtml(i.name)}</span>
                        ${i.notes ? `<span class="item-note">⚠ ${escapeHtml(i.notes)}</span>` : ''}
                    </div>
                    <button class="item-status-btn s-new" id="btn-${i.id}" data-status="new"
                            onclick="cycleStatus(${i.id},${data.order_id},${data.table_number})">NUEVO</button>
                </div>`);
        }
    });
    // Eliminar ítems removidos
    existing.forEach(id => { if (!incoming.has(id)) document.getElementById('item-row-' + id)?.remove(); });

    const fc = document.getElementById('footer-count-' + data.order_id);
    if (fc) {
        const openedTime = data.opened_at
            ? new Date(data.opened_at).toLocaleTimeString('es-AR', { hour:'2-digit', minute:'2-digit' }) : '—';
        fc.innerHTML = `${comidaItems.length} ítem(s) &nbsp;·&nbsp; ${openedTime}`;
    }
}

/* ── Contador de mesas ───────────────────────────── */
function updateMesasCount() {
    const count = document.querySelectorAll('.order-card').length;
    const lbl   = document.getElementById('lbl-mesas');
    if (lbl) lbl.textContent = `${count} ${count === 1 ? 'mesa' : 'mesas'}`;
    if (count === 0) {
        const grid = document.getElementById('kds-grid');
        if (!grid.querySelector('.kds-empty')) {
            grid.innerHTML = `<div class="kds-empty"><span>🎉</span>
                <p style="font-size:1rem;font-weight:700;color:#6b7280;">Sin pedidos activos</p>
                <p style="font-size:0.8rem;margin-top:.25rem;">La cocina está al día.</p></div>`;
        }
    }
}

/* ── Reloj de minutos ────────────────────────────── */
setInterval(() => {
    document.querySelectorAll('.order-card').forEach(card => {
        const oid  = card.id.replace('card-order-', '');
        const ts   = card.dataset.openedAt;
        if (!ts) return;
        const mins = Math.floor((Date.now() - new Date(ts).getTime()) / 60000);
        const tEl  = document.getElementById('time-' + oid);
        if (tEl) {
            tEl.textContent = mins + 'm';
            tEl.className   = 'card-time ' + (mins >= 30 ? 'time-urgent' : mins >= 15 ? 'time-warning' : 'time-ok');
        }
        card.classList.toggle('urgent',  mins >= 30);
        card.classList.toggle('warning', mins >= 15 && mins < 30);
    });
}, 30000);

/* ── WebSocket Echo ──────────────────────────────── */
function initEcho() {
    if (typeof window.Echo === 'undefined') { setTimeout(initEcho, 500); return; }
    const badge = document.getElementById('ws-badge');
    window.Echo.connector.pusher.connection.bind('connected',    () => {
        badge.textContent = '⚡ En vivo';
        badge.className   = 'kds-badge badge-ws-on';
    });
    window.Echo.connector.pusher.connection.bind('disconnected', () => {
        badge.textContent = '✕ Sin conexión';
        badge.className   = 'kds-badge badge-ws-off';
    });

    window.Echo.channel('restaurant')
        .listen('.order.updated', (data) => {
            document.getElementById('kds-empty-msg')?.remove();

            if (data.action === 'closed' || data.action === 'cancelled') {
                document.getElementById('card-order-' + data.order_id)?.remove();
                updateMesasCount();
                return;
            }

            if (data.action === 'entregado') {
                const card = document.getElementById('card-order-' + data.order_id);
                if (card) {
                    const label = data.is_delivery
                        ? '🛵 ' + (data.delivery_label || 'Delivery')
                        : 'Mesa ' + data.table_number;
                    showToast('✓ ' + label + ' — Pedido entregado');
                    addToEntregados(data);
                    card.style.transition = 'opacity .6s, transform .6s';
                    card.style.opacity    = '0';
                    card.style.transform  = 'scale(.97)';
                    setTimeout(() => { card.remove(); updateMesasCount(); }, 650);
                }
                return;
            }

            // Solo procesar si hay ítems de comida
            const comidaItems = soloComida(data.items);

            if (document.getElementById('card-order-' + data.order_id)) {
                syncItems(data);
                // Si después de sync no quedan ítems de comida, quitar la tarjeta
                const container = document.getElementById('items-' + data.order_id);
                if (container && container.querySelectorAll('[id^="item-row-"]').length === 0) {
                    document.getElementById('card-order-' + data.order_id)?.remove();
                    updateMesasCount();
                }
            } else if (comidaItems.length > 0) {
                const cardHtml = renderCard(data);
                if (cardHtml) {
                    document.getElementById('kds-grid').insertAdjacentHTML('beforeend', cardHtml);
                    updateMesasCount();
                }
            }
        })
        .listen('.kitchen.status', (data) => {
            applyItemStatus(data.item_id, data.status);
        });
}

document.addEventListener('DOMContentLoaded', initEcho);

/* ── Polling de sincronización completa cada 10s ─────────────────────────
   Agrega tarjetas que no están en pantalla Y elimina las que ya no existen.
   Actúa como respaldo cuando WebSocket falla o pierde un evento.
   ──────────────────────────────────────────────────────────────────────── */
async function pollSync() {
    try {
        const r    = await fetch('/api/kitchen/orders', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        const orders = await r.json();

        const activeSet = new Set(orders.map(o => String(o.order_id)));

        /* 1. Eliminar tarjetas que ya no están activas */
        document.querySelectorAll('.order-card').forEach(card => {
            const oid = card.id.replace('card-order-', '');
            if (!activeSet.has(oid)) {
                card.style.transition = 'opacity .5s, transform .5s';
                card.style.opacity    = '0';
                card.style.transform  = 'scale(.97)';
                setTimeout(() => { card.remove(); updateMesasCount(); }, 520);
            }
        });

        /* 2. Agregar tarjetas que no están en pantalla todavía */
        const grid = document.getElementById('kds-grid');
        orders.forEach(data => {
            if (document.getElementById('card-order-' + data.order_id)) return; // ya existe

            document.getElementById('kds-empty-msg')?.remove();

            const cardHtml = renderCard(data);
            if (cardHtml) {
                grid.insertAdjacentHTML('beforeend', cardHtml);
                updateMesasCount();
            }
        });
    } catch { /* silencioso */ }
}
setInterval(pollSync, 10000);

/* ── Sección Entregados ──────────────────────────── */
function addToEntregados(data) {
    const list = document.getElementById('entregados-list');
    if (!list) return;

    const label = data.is_delivery
        ? '🛵 ' + escapeHtml(data.delivery_label || 'Delivery')
        : 'Mesa ' + data.table_number;
    const now = new Date().toLocaleTimeString('es-AR', { hour:'2-digit', minute:'2-digit' });

    const card = document.createElement('div');
    card.className = 'entregado-card';
    card.dataset.orderId = data.order_id;
    card.innerHTML = `<span class="entregado-label">${label}</span>
                      <span class="entregado-time">${now}</span>`;
    list.appendChild(card);
    updateEntregadosSection();

    // Auto-eliminar después de 5 minutos
    setTimeout(() => { card.remove(); updateEntregadosSection(); }, 5 * 60 * 1000);
}

function updateEntregadosSection() {
    const section = document.getElementById('entregados-section');
    if (!section) return;
    const count = document.querySelectorAll('.entregado-card').length;
    section.style.display = count ? 'block' : 'none';
}

function limpiarEntregados() {
    document.getElementById('entregados-list')?.querySelectorAll('.entregado-card').forEach(c => c.remove());
    updateEntregadosSection();
}
</script>
</body>
</html>
