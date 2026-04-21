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

        .kds-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
            gap: 0.75rem; padding: 0.85rem 1rem;
        }

        .order-card {
            background: #161616; border: 1px solid #2a2a2a;
            border-radius: 0.875rem; overflow: hidden;
            display: flex; flex-direction: column;
        }
        .order-card.urgent  { border-color: #7f1d1d; }
        .order-card.warning { border-color: #78350f; }

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

        .card-items { padding: 0.5rem 0.6rem; flex: 1; }
        .item-row {
            display: flex; align-items: center; gap: 0.5rem;
            padding: 0.35rem 0.4rem; border-radius: 0.4rem; margin-bottom: 0.25rem;
        }
        .item-qty  { color: #9ca3af; font-size: 0.75rem; font-weight: 700; min-width: 1.8rem; text-align: right; }
        .item-name { flex: 1; font-size: 0.78rem; color: #e5e7eb; }
        .item-row.item-done .item-name { text-decoration: line-through; opacity: 0.4; }

        .item-status-btn {
            font-size: 0.6rem; font-weight: 700; padding: 0.18rem 0.45rem;
            border-radius: 999px; border: 1px solid; cursor: pointer;
            white-space: nowrap; letter-spacing: .03em; background: none;
        }
        .s-new       { color: #60a5fa; border-color: rgba(96,165,250,.4); }
        .s-new:hover { background: rgba(96,165,250,.12); }
        .s-preparing       { color: #fb923c; border-color: rgba(251,146,60,.4); }
        .s-preparing:hover { background: rgba(251,146,60,.12); }
        .s-ready       { color: #4ade80; border-color: rgba(74,222,128,.4); }
        .s-ready:hover { background: rgba(74,222,128,.12); }

        .card-footer {
            padding: 0.45rem 0.85rem; border-top: 1px solid #1f1f1f;
            font-size: 0.68rem; color: #4b5563;
            display: flex; justify-content: space-between;
        }

        .kds-empty {
            grid-column: 1 / -1; text-align: center;
            padding: 4rem 1rem; color: #374151;
        }
        .kds-empty span { font-size: 3rem; display: block; margin-bottom: 0.75rem; }
    </style>
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
    </div>
</header>

<main id="kds-grid" class="kds-grid">

@forelse($orders as $order)
    @php
        $mins   = $order->opened_at ? (int) $order->opened_at->diffInMinutes(now()) : 0;
        $tClass = $mins >= 30 ? 'time-urgent' : ($mins >= 15 ? 'time-warning' : 'time-ok');
        $cClass = $mins >= 30 ? 'urgent' : ($mins >= 15 ? 'warning' : '');
    @endphp
    <div class="order-card {{ $cClass }}" id="card-order-{{ $order->id }}" data-table="{{ $order->table->number }}">
        <div class="card-header">
            <div class="card-mesa">Mesa {{ $order->table->number }}</div>
            <div class="card-time {{ $tClass }}" id="time-{{ $order->id }}">{{ $mins }}m</div>
        </div>
        <div class="card-items" id="items-{{ $order->id }}">
            @foreach($order->items as $item)
                @php $ks = $item->kitchen_status ?? 'new'; @endphp
                <div class="item-row {{ $ks === 'ready' ? 'item-done' : '' }}" id="item-row-{{ $item->id }}">
                    <span class="item-qty">{{ $item->quantity }}×</span>
                    <span class="item-name">{{ $item->product->name }}</span>
                    <button class="item-status-btn s-{{ $ks }}"
                            id="btn-{{ $item->id }}"
                            data-status="{{ $ks }}"
                            onclick="cycleStatus({{ $item->id }}, {{ $order->id }}, {{ $order->table->number }})">
                        {{ $ks === 'new' ? 'NUEVO' : ($ks === 'preparing' ? 'PREP.' : 'LISTO') }}
                    </button>
                </div>
            @endforeach
        </div>
        <div class="card-footer">
            <span id="footer-count-{{ $order->id }}">{{ $order->items->count() }} ítem(s)</span>
            <span>
                <span id="ts-{{ $order->id }}" style="display:none" data-ts="{{ $order->opened_at?->toIso8601String() }}"></span>
                {{ $order->opened_at ? $order->opened_at->format('H:i') : '—' }}
            </span>
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

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const STATUS_CYCLE = { new:'preparing', preparing:'ready', ready:'new' };
const STATUS_LABEL = { new:'NUEVO', preparing:'PREP.', ready:'LISTO' };

/* ── Ciclar estado ítem ─────────────────────────── */
function cycleStatus(itemId, orderId, tableNum) {
    const btn = document.getElementById('btn-' + itemId);
    if (!btn) return;
    const current = btn.dataset.status;
    const next = STATUS_CYCLE[current] ?? 'new';
    applyItemStatus(itemId, next);   // optimistic

    fetch(`/kitchen/items/${itemId}/status`, {
        method: 'PUT',
        headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ status: next }),
    })
    .then(r => r.json())
    .then(d => { if (!d.success) applyItemStatus(itemId, current); })
    .catch(() => applyItemStatus(itemId, current));
}

function applyItemStatus(itemId, status) {
    const btn = document.getElementById('btn-' + itemId);
    const row = document.getElementById('item-row-' + itemId);
    if (!btn || !row) return;
    btn.dataset.status = status;
    btn.textContent    = STATUS_LABEL[status] ?? status;
    btn.className      = 'item-status-btn s-' + status;
    row.className      = 'item-row' + (status === 'ready' ? ' item-done' : '');
}

/* ── Render nueva tarjeta ───────────────────────── */
function renderCard(data) {
    const mins = data.opened_at
        ? Math.floor((Date.now() - new Date(data.opened_at).getTime()) / 60000) : 0;
    const tC = mins >= 30 ? 'time-urgent' : (mins >= 15 ? 'time-warning' : 'time-ok');
    const cC = mins >= 30 ? 'urgent' : (mins >= 15 ? 'warning' : '');
    const items = (data.items || []).map(i => `
        <div class="item-row" id="item-row-${i.id}">
            <span class="item-qty">${i.quantity}×</span>
            <span class="item-name">${i.name}</span>
            <button class="item-status-btn s-new" id="btn-${i.id}" data-status="new"
                    onclick="cycleStatus(${i.id},${data.order_id},${data.table_number})">NUEVO</button>
        </div>`).join('');
    const openedTime = data.opened_at
        ? new Date(data.opened_at).toLocaleTimeString('es-AR',{hour:'2-digit',minute:'2-digit'}) : '—';

    return `<div class="order-card ${cC}" id="card-order-${data.order_id}" data-table="${data.table_number}">
        <div class="card-header">
            <div class="card-mesa">Mesa ${data.table_number}</div>
            <div class="card-time ${tC}" id="time-${data.order_id}">${mins}m</div>
        </div>
        <div class="card-items" id="items-${data.order_id}">${items}</div>
        <div class="card-footer">
            <span id="footer-count-${data.order_id}">${(data.items||[]).length} ítem(s)</span>
            <span>
                <span id="ts-${data.order_id}" style="display:none" data-ts="${data.opened_at||''}"></span>
                ${openedTime}
            </span>
        </div>
    </div>`;
}

/* ── Sync ítems de tarjeta existente ────────────── */
function syncItems(data) {
    const container = document.getElementById('items-' + data.order_id);
    if (!container) return;
    const existing = new Set([...container.querySelectorAll('[id^="item-row-"]')]
        .map(el => parseInt(el.id.replace('item-row-','')))
    );
    const incoming = new Set((data.items||[]).map(i => i.id));

    // Agregar nuevos
    (data.items||[]).forEach(i => {
        if (!existing.has(i.id)) {
            container.insertAdjacentHTML('beforeend', `
                <div class="item-row" id="item-row-${i.id}">
                    <span class="item-qty">${i.quantity}×</span>
                    <span class="item-name">${i.name}</span>
                    <button class="item-status-btn s-new" id="btn-${i.id}" data-status="new"
                            onclick="cycleStatus(${i.id},${data.order_id},${data.table_number})">NUEVO</button>
                </div>`);
        }
    });
    // Eliminar removidos
    existing.forEach(id => { if (!incoming.has(id)) document.getElementById('item-row-'+id)?.remove(); });

    const fc = document.getElementById('footer-count-'+data.order_id);
    if (fc) fc.textContent = `${(data.items||[]).length} ítem(s)`;
}

function updateMesasCount() {
    const count = document.querySelectorAll('.order-card').length;
    const lbl = document.getElementById('lbl-mesas');
    if (lbl) lbl.textContent = `${count} ${count===1?'mesa':'mesas'}`;
    if (count === 0) {
        const grid = document.getElementById('kds-grid');
        if (!grid.querySelector('.kds-empty')) {
            grid.innerHTML = `<div class="kds-empty"><span>🎉</span>
                <p style="font-size:1rem;font-weight:700;color:#6b7280;">Sin pedidos activos</p>
                <p style="font-size:0.8rem;margin-top:.25rem;">La cocina está al día.</p></div>`;
        }
    }
}

/* ── Reloj de minutos ───────────────────────────── */
setInterval(() => {
    document.querySelectorAll('[id^="ts-"]').forEach(el => {
        if (!el.dataset.ts) return;
        const oid  = el.id.replace('ts-','');
        const mins = Math.floor((Date.now() - new Date(el.dataset.ts).getTime()) / 60000);
        const tEl  = document.getElementById('time-'+oid);
        const cEl  = document.getElementById('card-order-'+oid);
        if (tEl) { tEl.textContent = mins+'m'; tEl.className = 'card-time '+(mins>=30?'time-urgent':mins>=15?'time-warning':'time-ok'); }
        if (cEl) { cEl.classList.toggle('urgent',mins>=30); cEl.classList.toggle('warning',mins>=15&&mins<30); }
    });
}, 30000);

/* ── WebSocket Echo ─────────────────────────────── */
function initEcho() {
    if (typeof window.Echo === 'undefined') { setTimeout(initEcho, 500); return; }
    const badge = document.getElementById('ws-badge');
    window.Echo.connector.pusher.connection.bind('connected',    () => { badge.textContent='⚡ En vivo'; badge.className='kds-badge badge-ws-on'; });
    window.Echo.connector.pusher.connection.bind('disconnected', () => { badge.textContent='✕ Sin conexión'; badge.className='kds-badge badge-ws-off'; });

    window.Echo.channel('restaurant')
        .listen('.order.updated', (data) => {
            const empty = document.getElementById('kds-empty-msg');
            if (empty) empty.remove();

            if (data.action === 'closed' || data.action === 'cancelled') {
                document.getElementById('card-order-'+data.order_id)?.remove();
                updateMesasCount();
                return;
            }
            if (document.getElementById('card-order-'+data.order_id)) {
                syncItems(data);
            } else {
                document.getElementById('kds-grid').insertAdjacentHTML('beforeend', renderCard(data));
                updateMesasCount();
            }
        })
        .listen('.kitchen.status', (data) => {
            applyItemStatus(data.item_id, data.status);
        });
}
document.addEventListener('DOMContentLoaded', initEcho);
</script>
</body>
</html>
