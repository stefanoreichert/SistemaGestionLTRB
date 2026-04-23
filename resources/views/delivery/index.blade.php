<x-app-layout>
    <x-slot name="header">
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem;">
            <div style="display:flex; align-items:center; gap:.5rem;">
                <div style="display:flex; align-items:center; gap:.5rem; padding:.35rem .85rem;
                            border-radius:.5rem; background:rgba(99,102,241,.12); border:1px solid rgba(99,102,241,.3);">
                    <span id="del-dot-free" style="width:8px;height:8px;border-radius:50%;background:#818cf8;
                                 animation:del-pulse 2s cubic-bezier(.4,0,.6,1) infinite;"></span>
                    <span id="del-lbl-free" style="color:#a5b4fc;font-size:.8rem;font-weight:700;">{{ $free }} disponibles</span>
                </div>
                <div style="display:flex; align-items:center; gap:.5rem; padding:.35rem .85rem;
                            border-radius:.5rem; background:rgba(139,92,246,.12); border:1px solid rgba(139,92,246,.3);">
                    <span style="width:8px;height:8px;border-radius:50%;background:#8b5cf6;"></span>
                    <span id="del-lbl-occ" style="color:#c4b5fd;font-size:.8rem;font-weight:700;">{{ $occupied }} activos</span>
                </div>
            </div>
            <span style="font-size:.7rem;color:#4b5563;">
                Click = abrir &nbsp;&bull;&nbsp; Doble click = resumen
            </span>
        </div>
    </x-slot>

    <style>
        @keyframes del-pulse { 0%,100%{opacity:1} 50%{opacity:.35} }

        /* ── Slot button ──────────────────────────────────── */
        .del-slot {
            position: relative;
            border-radius: .875rem;
            border: 2px solid;
            padding: .875rem .5rem;
            text-align: center;
            cursor: pointer;
            user-select: none;
            background: none;
            width: 100%;
            display: block;
            transition: transform .14s, box-shadow .14s, border-color .14s, background .14s;
        }
        .del-slot:active { transform: scale(.93) !important; }

        /* Libre */
        .del-libre { background: rgba(15,10,50,.5); border-color: rgba(99,102,241,.25); color: #818cf8; }
        .del-libre:hover {
            background: rgba(30,20,80,.7); border-color: #6366f1;
            transform: scale(1.04); box-shadow: 0 0 18px rgba(99,102,241,.25);
        }
        /* Activo */
        .del-activo { background: rgba(40,10,90,.5); border-color: rgba(139,92,246,.4); color: #c4b5fd; }
        .del-activo:hover {
            background: rgba(55,15,110,.7); border-color: #8b5cf6;
            transform: scale(1.04); box-shadow: 0 0 18px rgba(139,92,246,.3);
        }
        /* Listo */
        .del-listo { background: rgba(80,50,0,.45); border-color: rgba(234,179,8,.5); color: #fef08a; }
        .del-listo:hover {
            background: rgba(100,65,0,.65); border-color: #eab308;
            transform: scale(1.04); box-shadow: 0 0 18px rgba(234,179,8,.3);
        }

        .del-num   { font-size: 1.6rem; font-weight: 800; line-height: 1; }
        .del-state { font-size: .58rem; font-weight: 700; letter-spacing: .1em;
                     text-transform: uppercase; margin-top: .3rem; opacity: .65; }
        .del-lbl   { font-size: .63rem; font-weight: 600; margin-top: .2rem;
                     overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 100%; }
        .del-meta  { font-size: .58rem; color: #a78bfa; margin-top: .15rem; opacity: .8; }
        .del-total { font-size: .68rem; font-weight: 800; color: #a78bfa; margin-top: .15rem; }
        .del-dot   { position: absolute; top: .4rem; right: .45rem;
                     width: 7px; height: 7px; border-radius: 50%; }
        .del-deliver-btn {
            display: block; width: 100%; margin-top: .4rem;
            padding: .28rem .4rem; font-size: .56rem; font-weight: 700;
            letter-spacing: .05em; text-transform: uppercase;
            cursor: pointer; border-radius: .35rem;
            background: rgba(234,179,8,.2); color: #fef08a;
            border: 1px solid rgba(234,179,8,.45); transition: background .15s;
        }
        .del-deliver-btn:hover { background: rgba(234,179,8,.38); }

        /* Grid responsive */
        .del-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: .6rem; }
        @media(min-width:480px)  { .del-grid { grid-template-columns: repeat(5,1fr); } }
        @media(min-width:640px)  { .del-grid { grid-template-columns: repeat(6,1fr); } }
        @media(min-width:900px)  { .del-grid { grid-template-columns: repeat(8,1fr); } }
        @media(min-width:1200px) { .del-grid { grid-template-columns: repeat(10,1fr); } }
        @media(max-width:479px)  { .del-num { font-size: 2rem !important; } .del-slot { min-height: 75px; } }
    </style>

    <div style="padding:1.25rem;">
        @if(session('success'))
            <div style="margin-bottom:1rem;padding:.7rem 1rem;border-radius:.5rem;
                        background:rgba(99,102,241,.1);border:1px solid rgba(99,102,241,.3);
                        color:#a5b4fc;font-size:.82rem;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div style="margin-bottom:1rem;padding:.7rem 1rem;border-radius:.5rem;
                        background:rgba(220,38,38,.1);border:1px solid rgba(220,38,38,.3);
                        color:#f87171;font-size:.82rem;">{{ session('error') }}</div>
        @endif

        <div class="del-grid" id="del-grid">
            @foreach($slots as $slot)
                @php
                    $isListo = !$slot['is_free'] && $slot['kitchen_status'] === 'listo';
                    if ($slot['is_free']) {
                        $cls = 'del-libre'; $dot = '#6366f1'; $dotAnim = 'animation:del-pulse 2s infinite;';
                    } elseif ($isListo) {
                        $cls = 'del-listo'; $dot = '#eab308'; $dotAnim = '';
                    } else {
                        $cls = 'del-activo'; $dot = '#8b5cf6'; $dotAnim = '';
                    }
                @endphp

                <div id="slot-{{ $slot['number'] }}"
                     class="del-slot {{ $cls }}"
                     data-num="{{ $slot['number'] }}"
                     data-free="{{ $slot['is_free'] ? '1' : '0' }}"
                     data-order-id="{{ $slot['order']?->id ?? '' }}"
                     onclick="delClick({{ $slot['number'] }})"
                     ondblclick="delDblClick({{ $slot['number'] }}, event)"
                     title="Delivery #{{ $slot['number'] }}{{ $slot['label'] ? ' — '.$slot['label'] : '' }}">

                    <span class="del-dot" style="background:{{ $dot }};{{ $dotAnim }}"></span>
                    <div class="del-num">{{ $slot['number'] }}</div>
                    <div class="del-state">
                        {{ $slot['is_free'] ? 'libre' : ($isListo ? 'listo' : 'activo') }}
                    </div>

                    @if(!$slot['is_free'])
                        <div class="del-lbl" title="{{ $slot['label'] }}">
                            {{ Str::limit($slot['label'], 14) }}
                        </div>
                        <div class="del-meta">
                            {{ $slot['items_count'] }} ítem{{ $slot['items_count'] !== 1 ? 's' : '' }}
                            &bull; {{ $slot['opened_at'] }}
                        </div>
                        @if($slot['total'] > 0)
                            <div class="del-total">${{ number_format($slot['total'], 0, ',', '.') }}</div>
                        @endif
                        @if($slot['items_count'] > 0)
                            <button id="dbtn-{{ $slot['number'] }}"
                                    class="del-deliver-btn"
                                    style="{{ $isListo ? '' : 'display:none;' }}"
                                    onclick="event.stopPropagation(); delEntregar({{ $slot['order']?->id }}, {{ $slot['number'] }}, this)">
                                Entregar pedido
                            </button>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── Modal: nuevo delivery ──────────────────────────────────── --}}
    <div id="del-modal-nuevo"
         style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.8);
                z-index:200;align-items:center;justify-content:center;padding:1rem;"
         onclick="delCerrarNuevo()">
        <div style="background:#161616;border:1px solid rgba(99,102,241,.45);border-radius:1rem;
                    box-shadow:0 25px 60px rgba(0,0,0,.75);padding:1.5rem;
                    max-width:22rem;width:100%;"
             onclick="event.stopPropagation()">
            <h3 style="font-weight:800;font-size:1rem;color:#a5b4fc;margin-bottom:.2rem;">
                🛵 Delivery #<span id="del-modal-num"></span>
            </h3>
            <p style="font-size:.75rem;color:#6b7280;margin-bottom:1rem;">
                Nombre del cliente o referencia del pedido
            </p>
            <input id="del-modal-input" type="text" maxlength="100"
                   placeholder="Ej: Juan García, Pedido #12…"
                   style="width:100%;box-sizing:border-box;background:#1e1e1e;border:1px solid #333;
                          color:#e5e7eb;border-radius:.5rem;padding:.6rem .75rem;font-size:.88rem;
                          margin-bottom:1rem;"
                   onfocus="this.style.borderColor='#6366f1'"
                   onblur="this.style.borderColor='#333'"
                   onkeydown="if(event.key==='Enter')delConfirmarNuevo();if(event.key==='Escape')delCerrarNuevo()">
            <div id="del-modal-err" style="display:none;font-size:.72rem;color:#f87171;margin-bottom:.6rem;"></div>
            <div style="display:flex;gap:.5rem;justify-content:flex-end;">
                <button onclick="delCerrarNuevo()"
                        style="padding:.5rem 1rem;font-size:.78rem;color:#9ca3af;
                               border:1px solid #333;border-radius:.5rem;background:#1a1a1a;cursor:pointer;"
                        onmouseover="this.style.background='#222'"
                        onmouseout="this.style.background='#1a1a1a'">Cancelar</button>
                <button id="del-modal-btn" onclick="delConfirmarNuevo()"
                        style="padding:.5rem 1.1rem;font-size:.78rem;color:#fff;
                               background:#4f46e5;border:none;border-radius:.5rem;
                               font-weight:700;cursor:pointer;"
                        onmouseover="this.style.background='#6366f1'"
                        onmouseout="this.style.background='#4f46e5'">Abrir Delivery</button>
            </div>
        </div>
    </div>

    {{-- ── Modal: resumen rápido (doble click) ───────────────────── --}}
    <div id="del-modal-resumen"
         style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.75);
                z-index:200;align-items:center;justify-content:center;padding:1rem;"
         onclick="delCerrarResumen()">
        <div style="background:#161616;border:1px solid rgba(139,92,246,.4);border-radius:1rem;
                    box-shadow:0 25px 60px rgba(0,0,0,.75);padding:1.5rem;
                    max-width:24rem;width:100%;max-height:80vh;overflow-y:auto;"
             onclick="event.stopPropagation()">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
                <h3 id="del-res-titulo" style="font-weight:800;font-size:.95rem;color:#a78bfa;"></h3>
                <button onclick="delCerrarResumen()"
                        style="background:none;border:none;color:#6b7280;cursor:pointer;font-size:1.2rem;line-height:1;">
                    &times;
                </button>
            </div>
            <div id="del-res-body" style="font-size:.82rem;color:#e5e7eb;"></div>
            <div style="display:flex;gap:.5rem;margin-top:1rem;justify-content:flex-end;flex-wrap:wrap;">
                <button id="del-res-btn-entregar"
                        style="display:none;padding:.5rem 1rem;font-size:.76rem;color:#fef08a;
                               background:rgba(234,179,8,.18);border:1px solid rgba(234,179,8,.4);
                               border-radius:.5rem;font-weight:700;cursor:pointer;"
                        onclick="delEntregarDesdeResumen()">
                    ✓ Marcar entregado
                </button>
                <a id="del-res-btn-abrir" href="#"
                   style="padding:.5rem 1.1rem;font-size:.78rem;color:#fff;
                          background:#4f46e5;border:none;border-radius:.5rem;
                          font-weight:700;text-decoration:none;">
                    Abrir pedido →
                </a>
            </div>
        </div>
    </div>

    <script>
    (function () {
        'use strict';

        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        /* ── Estado JS sincronizado con slots del servidor ──── */
        let slotsData = @json($slots->keyBy('number'));
        let _resumenOrderId = null;
        let _pollTimer      = null;

        /* Iniciar polling ligero cada 20 s */
        function startPoll() {
            _pollTimer = setInterval(pollSlots, 20000);
        }

        async function pollSlots() {
            try {
                const r    = await fetch('/api/delivery', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
                });
                const list = await r.json();

                /* Rebuild slotsData desde la respuesta */
                const occupied = {};
                list.forEach(d => { occupied[d.delivery_number] = d; });

                for (let n = 1; n <= 20; n++) {
                    const d = occupied[n] || null;
                    updateSlotCard(n, d);
                }
                updateCounters();
            } catch { /* silencioso */ }
        }

        function updateSlotCard(num, d) {
            const el = document.getElementById('slot-' + num);
            if (!el) return;

            if (!d) {
                /* Libre */
                el.className = 'del-slot del-libre';
                el.dataset.free = '1';
                el.dataset.orderId = '';
                el.querySelector('.del-state').textContent = 'libre';
                const lbl = el.querySelector('.del-lbl');
                if (lbl) lbl.remove();
                const meta = el.querySelector('.del-meta');
                if (meta) meta.remove();
                const tot = el.querySelector('.del-total');
                if (tot) tot.remove();
                const btn = el.querySelector('.del-deliver-btn');
                if (btn) btn.remove();
                const dot = el.querySelector('.del-dot');
                if (dot) { dot.style.background = '#6366f1'; dot.style.animation = 'del-pulse 2s infinite'; }
                /* Actualizar slotsData */
                slotsData[num] = { number: num, is_free: true };
            } else {
                const isListo = d.kitchen_status === 'listo';
                el.className = 'del-slot ' + (isListo ? 'del-listo' : 'del-activo');
                el.dataset.free    = '0';
                el.dataset.orderId = d.order_id;
                el.querySelector('.del-state').textContent = isListo ? 'listo' : 'activo';
                const dot = el.querySelector('.del-dot');
                if (dot) { dot.style.background = isListo ? '#eab308' : '#8b5cf6'; dot.style.animation = ''; }
                /* Mostrar/ocultar botón entregar */
                let btn = document.getElementById('dbtn-' + num);
                if (!btn && d.items_count > 0) {
                    btn = document.createElement('button');
                    btn.id = 'dbtn-' + num;
                    btn.className = 'del-deliver-btn';
                    btn.textContent = 'Entregar pedido';
                    btn.onclick = function (e) {
                        e.stopPropagation();
                        delEntregar(d.order_id, num, btn);
                    };
                    el.appendChild(btn);
                }
                if (btn) btn.style.display = isListo ? 'block' : 'none';
                /* Actualizar slotsData */
                slotsData[num] = {
                    number: num, is_free: false,
                    kitchen_status: d.kitchen_status,
                    items_count: d.items_count,
                    order: { id: d.order_id },
                    label: d.delivery_label,
                    opened_at: d.opened_time,
                };
            }
        }

        function updateCounters() {
            let free = 0, occ = 0;
            for (let n = 1; n <= 20; n++) {
                const s = slotsData[n];
                if (s && !s.is_free) occ++; else free++;
            }
            const lf = document.getElementById('del-lbl-free');
            const lo = document.getElementById('del-lbl-occ');
            if (lf) lf.textContent = free + ' disponibles';
            if (lo) lo.textContent = occ + ' activos';
        }

        /* ── CLICK SIMPLE: abrir ─────────────────────────── */
        window.delClick = function (num) {
            const slot = slotsData[num];
            if (!slot) return;

            if (slot.is_free) {
                /* Slot libre → modal para crear */
                document.getElementById('del-modal-num').textContent = num;
                document.getElementById('del-modal-input').value = '';
                document.getElementById('del-modal-err').style.display = 'none';
                document.getElementById('del-modal-btn').disabled = false;
                document.getElementById('del-modal-btn').textContent = 'Abrir Delivery';
                const modal = document.getElementById('del-modal-nuevo');
                modal.style.display = 'flex';
                modal._slotNum = num;
                setTimeout(() => document.getElementById('del-modal-input').focus(), 60);
            } else {
                /* Slot ocupado → navegar directamente */
                window.location.href = '/delivery/' + slot.order.id;
            }
        };

        /* ── DOBLE CLICK: resumen rápido ─────────────────── */
        window.delDblClick = function (num, event) {
            event.stopPropagation();
            const slot = slotsData[num];
            if (!slot || slot.is_free) return;

            _resumenOrderId = slot.order.id;

            document.getElementById('del-res-titulo').textContent =
                '🛵 Delivery #' + num + ' — ' + (slot.label || '');
            document.getElementById('del-res-btn-abrir').href = '/delivery/' + slot.order.id;

            const btnE = document.getElementById('del-res-btn-entregar');
            btnE.style.display = (slot.kitchen_status === 'listo') ? 'block' : 'none';
            btnE.dataset.orderId = slot.order.id;
            btnE.dataset.num     = num;

            const body = document.getElementById('del-res-body');
            body.innerHTML = '<p style="color:#6b7280;font-size:.8rem;">Cargando…</p>';
            document.getElementById('del-modal-resumen').style.display = 'flex';

            /* Cargar ítems via API */
            fetch('/delivery/' + slot.order.id + '/summary', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    body.innerHTML = '<p style="color:#6b7280;font-style:italic;">' +
                        _esc(data.message || 'Sin ítems aún.') + '</p>';
                    return;
                }
                let html = '<table style="width:100%;border-collapse:collapse;font-size:.8rem;">';
                data.items.forEach(i => {
                    html += '<tr style="border-bottom:1px solid #1e1e1e;">' +
                        '<td style="padding:.4rem .25rem;color:#e5e7eb;">' + _esc(i.name) + '</td>' +
                        '<td style="padding:.4rem;text-align:center;color:#6b7280;white-space:nowrap;">x' + i.quantity + '</td>' +
                        '<td style="padding:.4rem .25rem;text-align:right;color:#4ade80;font-weight:700;">' +
                        '$' + _fmt(i.subtotal) + '</td></tr>';
                });
                html += '</table>';
                html += '<div style="display:flex;justify-content:space-between;padding:.7rem .25rem 0;' +
                    'border-top:1px solid #222;margin-top:.5rem;">' +
                    '<span style="font-weight:700;color:#a78bfa;">Total</span>' +
                    '<span style="font-size:1.1rem;font-weight:900;color:#c4b5fd;">$' + _fmt(data.total) + '</span></div>';
                body.innerHTML = html;
            })
            .catch(() => {
                body.innerHTML = '<p style="color:#f87171;">Error al cargar ítems.</p>';
            });
        };

        /* ── Confirmar nuevo delivery ────────────────────── */
        window.delConfirmarNuevo = async function () {
            const modal = document.getElementById('del-modal-nuevo');
            const num   = modal._slotNum;
            const label = document.getElementById('del-modal-input').value.trim();
            const errEl = document.getElementById('del-modal-err');
            const btn   = document.getElementById('del-modal-btn');

            errEl.style.display = 'none';
            if (!label) {
                errEl.textContent = 'Ingresá un nombre o referencia.';
                errEl.style.display = 'block';
                document.getElementById('del-modal-input').focus();
                return;
            }

            btn.disabled    = true;
            btn.textContent = 'Creando…';

            try {
                const r = await fetch('/api/delivery', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ label, delivery_number: num }),
                });
                const data = await r.json();
                if (data.success) {
                    delCerrarNuevo();
                    window.location.href = data.url;
                } else if (data.url) {
                    delCerrarNuevo();
                    window.location.href = data.url;
                } else {
                    errEl.textContent = data.message || 'Error al crear el delivery.';
                    errEl.style.display = 'block';
                    btn.disabled    = false;
                    btn.textContent = 'Abrir Delivery';
                }
            } catch {
                errEl.textContent = 'Error de conexión.';
                errEl.style.display = 'block';
                btn.disabled    = false;
                btn.textContent = 'Abrir Delivery';
            }
        };

        /* ── Entregar desde grid ────────────────────────── */
        window.delEntregar = async function (orderId, num, btn) {
            if (!confirm('¿Confirmar entrega del Delivery #' + num + '?')) return;
            btn.disabled    = true;
            btn.textContent = '…';
            try {
                const r    = await fetch('/delivery/' + orderId + '/deliver', {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                });
                const data = await r.json();
                if (data.success) {
                    updateSlotCard(num, null);
                    updateCounters();
                } else {
                    btn.disabled    = false;
                    btn.textContent = 'Entregar pedido';
                }
            } catch {
                btn.disabled    = false;
                btn.textContent = 'Entregar pedido';
            }
        };

        /* ── Entregar desde modal resumen ───────────────── */
        window.delEntregarDesdeResumen = async function () {
            const btnE = document.getElementById('del-res-btn-entregar');
            const oid  = parseInt(btnE.dataset.orderId);
            const num  = parseInt(btnE.dataset.num);
            if (!oid || !confirm('¿Confirmar entrega?')) return;
            btnE.disabled    = true;
            btnE.textContent = '…';
            try {
                const r    = await fetch('/delivery/' + oid + '/deliver', {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                });
                const data = await r.json();
                if (data.success) {
                    delCerrarResumen();
                    updateSlotCard(num, null);
                    updateCounters();
                } else {
                    btnE.disabled    = false;
                    btnE.textContent = '✓ Marcar entregado';
                }
            } catch {
                btnE.disabled    = false;
                btnE.textContent = '✓ Marcar entregado';
            }
        };

        /* ── Cerrar modales ─────────────────────────────── */
        window.delCerrarNuevo    = function () {
            document.getElementById('del-modal-nuevo').style.display = 'none';
        };
        window.delCerrarResumen  = function () {
            document.getElementById('del-modal-resumen').style.display = 'none';
            _resumenOrderId = null;
        };

        /* ── Helpers ────────────────────────────────────── */
        function _esc(str) {
            return String(str ?? '')
                .replace(/&/g,'&amp;').replace(/</g,'&lt;')
                .replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
        }
        function _fmt(n) {
            return Number(n).toLocaleString('es-AR',{ minimumFractionDigits:0, maximumFractionDigits:0 });
        }

        /* ── Teclado global ────────────────────────────── */
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') { delCerrarNuevo(); delCerrarResumen(); }
        });

        /* ── SPA: registrar cleanup ─────────────────────── */
        function __pageInit() {
            startPoll();
            if (typeof window.spaRegisterCleanup === 'function') {
                window.spaRegisterCleanup(function () {
                    if (_pollTimer) { clearInterval(_pollTimer); _pollTimer = null; }
                });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', __pageInit);
        } else {
            __pageInit();
        }
    })();
    </script>
</x-app-layout>
