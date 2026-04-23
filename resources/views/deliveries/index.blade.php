<x-app-layout>
    <x-slot name="header">
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.75rem;">

            <div style="display:flex; align-items:center; gap:0.5rem;">
                <div style="display:flex; align-items:center; gap:0.5rem; padding:0.35rem 0.85rem;
                            border-radius:0.5rem; background:rgba(99,102,241,0.15); border:1px solid rgba(99,102,241,0.3);">
                    <span style="width:8px; height:8px; border-radius:50%; background:#818cf8;
                                 animation:pulse 2s cubic-bezier(0.4,0,0.6,1) infinite;"></span>
                    <span style="color:#a5b4fc; font-size:0.8rem; font-weight:700;">{{ $free }} disponibles</span>
                </div>
                <div style="display:flex; align-items:center; gap:0.5rem; padding:0.35rem 0.85rem;
                            border-radius:0.5rem; background:rgba(139,92,246,0.12); border:1px solid rgba(139,92,246,0.3);">
                    <span style="width:8px; height:8px; border-radius:50%; background:#8b5cf6;"></span>
                    <span style="color:#c4b5fd; font-size:0.8rem; font-weight:700;">{{ $occupied }} activos</span>
                </div>
            </div>

            <span style="font-size:0.72rem; color:#4b5563;">
                Click = abrir &nbsp;&bull;&nbsp; Doble click = resumen
            </span>

        </div>
    </x-slot>

    <style>
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }

        .del-btn {
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
        .del-btn:active { transform: scale(0.93) !important; }

        .del-libre {
            background: rgba(30,27,75,0.45);
            border-color: rgba(99,102,241,0.3);
            color: #a5b4fc;
        }
        .del-libre:hover {
            background: rgba(30,27,75,0.7);
            border-color: #6366f1;
            transform: scale(1.05);
            box-shadow: 0 0 18px rgba(99,102,241,0.25);
        }
        .del-ocupado {
            background: rgba(60,20,120,0.45);
            border-color: rgba(139,92,246,0.45);
            color: #c4b5fd;
        }
        .del-ocupado:hover {
            background: rgba(60,20,120,0.7);
            border-color: #8b5cf6;
            transform: scale(1.05);
            box-shadow: 0 0 18px rgba(139,92,246,0.3);
        }
        .del-listo {
            background: rgba(120,83,0,0.35);
            border-color: rgba(234,179,8,0.55);
            color: #fef08a;
        }
        .del-listo:hover {
            background: rgba(120,83,0,0.55);
            border-color: #eab308;
            transform: scale(1.05);
            box-shadow: 0 0 18px rgba(234,179,8,0.35);
        }

        .del-num   { font-size: 1.6rem; font-weight: 800; line-height: 1; }
        .del-estado {
            font-size: 0.6rem; font-weight: 600; letter-spacing: 0.08em;
            text-transform: uppercase; margin-top: 0.3rem; opacity: 0.7;
        }
        .del-label {
            font-size: 0.65rem; font-weight: 600; margin-top: 0.2rem;
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
            max-width: 100%;
        }
        .del-meta {
            font-size: 0.6rem; color: #a78bfa; margin-top: 0.15rem; opacity: 0.8;
        }
        .del-dot {
            position: absolute; top: 0.45rem; right: 0.5rem;
            width: 7px; height: 7px; border-radius: 50%;
        }
        .btn-entregar-del {
            display: block; width: 100%; margin-top: 0.45rem; padding: 0.3rem 0.4rem;
            font-size: 0.58rem; font-weight: 700; letter-spacing: .05em;
            text-transform: uppercase; cursor: pointer;
            background: rgba(234,179,8,.22); color: #fef08a;
            border: 1px solid rgba(234,179,8,.5); border-radius: 0.35rem;
            transition: background .15s;
        }
        .btn-entregar-del:hover { background: rgba(234,179,8,.38); }

        /* Grid responsive */
        .del-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.6rem; }
        @media (min-width: 480px)  { .del-grid { grid-template-columns: repeat(5, 1fr); } }
        @media (min-width: 640px)  { .del-grid { grid-template-columns: repeat(6, 1fr); } }
        @media (min-width: 900px)  { .del-grid { grid-template-columns: repeat(8, 1fr); } }
        @media (min-width: 1200px) { .del-grid { grid-template-columns: repeat(10, 1fr); } }
        /* Móvil: celdas más grandes */
        @media (max-width: 479px) {
            .del-num { font-size: 2rem !important; }
            .del-btn { padding: 1rem .5rem !important; min-height: 75px; }
        }
    </style>

    <div style="padding: 1.25rem;">

        @if(session('success'))
            <div style="margin-bottom:1rem; padding:0.75rem 1rem; border-radius:0.5rem;
                        background:rgba(99,102,241,0.1); border:1px solid rgba(99,102,241,0.3);
                        color:#a5b4fc; font-size:0.82rem;">
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

        {{-- Grilla de 20 slots --}}
        <div class="del-grid">
            @foreach($slots as $slot)
                @php
                    $isListo   = !$slot['is_free'] && $slot['kitchen_status'] === 'listo';
                    $cardClass = $slot['is_free'] ? 'del-libre' : ($isListo ? 'del-listo' : 'del-ocupado');
                    $dotColor  = $slot['is_free'] ? '#6366f1' : ($isListo ? '#eab308' : '#8b5cf6');
                    $orderId   = $slot['order']?->id;
                @endphp
                <div
                    id="del-btn-{{ $slot['number'] }}"
                    class="del-btn {{ $cardClass }}"
                    data-number="{{ $slot['number'] }}"
                    data-order-id="{{ $orderId ?? '' }}"
                    data-kitchen-status="{{ $slot['kitchen_status'] }}"
                    onclick="abrirDelivery({{ $slot['number'] }})"
                    ondblclick="verResumenDelivery({{ $slot['number'] }}, event)"
                    title="Delivery #{{ $slot['number'] }}{{ $slot['label'] ? ' — '.$slot['label'] : '' }}">

                    <span class="del-dot"
                          style="background:{{ $dotColor }};{{ $slot['is_free'] ? 'animation:pulse 2s infinite;' : '' }}"></span>

                    <div class="del-num">{{ $slot['number'] }}</div>
                    <div class="del-estado">
                        {{ $slot['is_free'] ? 'libre' : ($isListo ? 'listo' : 'activo') }}
                    </div>

                    @if(!$slot['is_free'])
                        <div class="del-label" title="{{ $slot['label'] }}">
                            {{ Str::limit($slot['label'], 16) }}
                        </div>
                        <div class="del-meta">
                            {{ $slot['items_count'] }} item{{ $slot['items_count'] !== 1 ? 's' : '' }}
                            &bull; {{ $slot['opened_at'] }}
                        </div>
                        @if($slot['items_count'] > 0)
                        <div style="font-size:.68rem; font-weight:800; color:#a78bfa; margin-top:.2rem;">
                            ${{ number_format($slot['total'], 0, ',', '.') }}
                        </div>
                        @endif

                        <button id="btn-entregar-del-{{ $slot['number'] }}"
                                class="btn-entregar-del {{ $isListo ? '' : 'btn-entregar-del-hidden' }}"
                                style="{{ $isListo ? '' : 'display:none;' }}"
                                onclick="event.stopPropagation(); entregarDelivery({{ $orderId }}, this)">
                            Pedido entregado
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Modal para nuevo delivery (slot libre) --}}
    <div id="modal-nuevo-delivery"
         style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.78);
                z-index:110; align-items:center; justify-content:center; padding:1rem;"
         onclick="cerrarModalNuevo()">
        <div style="background:#161616; border:1px solid rgba(99,102,241,.4); border-radius:1rem;
                    box-shadow:0 25px 60px rgba(0,0,0,.7); padding:1.5rem; max-width:22rem; width:100%;"
             onclick="event.stopPropagation()">
            <h3 style="font-weight:800; font-size:1rem; color:#a5b4fc; margin-bottom:.25rem;">
                🛵 Delivery #<span id="modal-slot-num"></span>
            </h3>
            <p style="font-size:.75rem; color:#6b7280; margin-bottom:1rem;">
                Nombre del cliente o referencia del pedido
            </p>

            <input id="modal-label-input"
                   type="text" maxlength="100" placeholder="Ej: Juan García, Pedido #12…"
                   style="width:100%; box-sizing:border-box; background:#1e1e1e; border:1px solid #333;
                          color:#e5e7eb; border-radius:.5rem; padding:.6rem .75rem; font-size:.88rem;
                          margin-bottom:1rem;"
                   onfocus="this.style.borderColor='#6366f1'"
                   onblur="this.style.borderColor='#333'"
                   onkeydown="if(event.key==='Enter') confirmarNuevoDelivery()">

            <div style="display:flex; gap:.5rem; justify-content:flex-end;">
                <button onclick="cerrarModalNuevo()"
                        style="padding:.5rem 1rem; font-size:.78rem; color:#9ca3af;
                               border:1px solid #333; border-radius:.5rem; background:#1a1a1a; cursor:pointer;"
                        onmouseover="this.style.background='#222'"
                        onmouseout="this.style.background='#1a1a1a'">Cancelar</button>
                <button onclick="confirmarNuevoDelivery()"
                        style="padding:.5rem 1.1rem; font-size:.78rem; color:#fff;
                               background:#4f46e5; border:none; border-radius:.5rem;
                               font-weight:700; cursor:pointer;"
                        onmouseover="this.style.background='#6366f1'"
                        onmouseout="this.style.background='#4f46e5'">Abrir Delivery</button>
            </div>
        </div>
    </div>

    {{-- Modal resumen rápido --}}
    <div id="modal-resumen"
         style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.72);
                z-index:120; align-items:center; justify-content:center; padding:1rem;"
         onclick="cerrarResumen()">
        <div style="background:#161616; border:1px solid rgba(139,92,246,.35); border-radius:1rem;
                    box-shadow:0 25px 60px rgba(0,0,0,.7); padding:1.5rem; max-width:24rem; width:100%;
                    max-height:80vh; overflow-y:auto;"
             onclick="event.stopPropagation()">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
                <h3 id="resumen-titulo" style="font-weight:800; font-size:1rem; color:#a78bfa;"></h3>
                <button onclick="cerrarResumen()"
                        style="background:none;border:none;color:#6b7280;cursor:pointer;font-size:1.2rem;line-height:1;">
                    &times;
                </button>
            </div>
            <div id="resumen-body" style="font-size:.82rem; color:#e5e7eb;"></div>
            <div style="display:flex;gap:.5rem;margin-top:1rem;justify-content:flex-end;">
                <a id="resumen-btn-abrir" href="#"
                   style="padding:.5rem 1.1rem; font-size:.78rem; color:#fff;
                          background:#4f46e5; border:none; border-radius:.5rem;
                          font-weight:700; text-decoration:none; cursor:pointer;">
                    Abrir pedido →
                </a>
            </div>
        </div>
    </div>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        // Mapa slot → datos del servidor (para JS en tiempo real)
        let slotsData = @json($slots->keyBy('number'));

        // ── Abrir slot ────────────────────────────────────────────
        function abrirDelivery(num) {
            const slot = slotsData[num];
            if (!slot) return;

            if (slot.is_free) {
                // Mostrar modal para nueva etiqueta
                document.getElementById('modal-slot-num').textContent = num;
                document.getElementById('modal-label-input').value = '';
                const modal = document.getElementById('modal-nuevo-delivery');
                modal.style.display = 'flex';
                setTimeout(() => document.getElementById('modal-label-input').focus(), 80);
                modal._slotNum = num;
            } else {
                // Navegar al pedido
                window.location.href = '/deliveries/' + slot.order.id;
            }
        }

        // ── Confirmar nuevo delivery ──────────────────────────────
        async function confirmarNuevoDelivery() {
            const modal   = document.getElementById('modal-nuevo-delivery');
            const num     = modal._slotNum;
            const label   = document.getElementById('modal-label-input').value.trim();

            if (!label) {
                document.getElementById('modal-label-input').style.borderColor = '#ef4444';
                document.getElementById('modal-label-input').focus();
                return;
            }

            try {
                const r = await fetch('/api/deliveries', {
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
                    cerrarModalNuevo();
                    window.location.href = data.url;
                } else if (data.url) {
                    // Slot ya ocupado → redirigir
                    cerrarModalNuevo();
                    window.location.href = data.url;
                } else {
                    alert(data.message ?? 'Error al crear el delivery.');
                }
            } catch {
                alert('Error de conexión.');
            }
        }

        function cerrarModalNuevo() {
            document.getElementById('modal-nuevo-delivery').style.display = 'none';
        }

        // ── Resumen rápido (doble click) ──────────────────────────
        function verResumenDelivery(num, event) {
            event.stopPropagation();
            const slot = slotsData[num];
            if (!slot || slot.is_free) return;

            const titulo = document.getElementById('resumen-titulo');
            const body   = document.getElementById('resumen-body');
            const btn    = document.getElementById('resumen-btn-abrir');

            titulo.textContent = `🛵 Delivery #${num} — ${slot.label}`;
            btn.href = '/deliveries/' + slot.order.id;

            // Mostrar ítems si los tiene
            if (slot.items_count === 0) {
                body.innerHTML = '<p style="color:#6b7280;font-style:italic;">Sin ítems aún.</p>';
            } else {
                // Cargamos datos frescos via API
                body.innerHTML = '<p style="color:#6b7280;">Cargando...</p>';
                fetch('/deliveries/' + slot.order.id + '/summary', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) {
                        body.innerHTML = `<p style="color:#6b7280;font-style:italic;">${data.message}</p>`;
                        return;
                    }
                    let html = `<table style="width:100%;border-collapse:collapse;font-size:.8rem;">`;
                    data.items.forEach(i => {
                        html += `<tr style="border-bottom:1px solid #1e1e1e;">
                            <td style="padding:.4rem .25rem;color:#e5e7eb;">${escHtml(i.name)}</td>
                            <td style="padding:.4rem;text-align:center;color:#6b7280;">x${i.quantity}</td>
                            <td style="padding:.4rem .25rem;text-align:right;color:#4ade80;font-weight:700;">
                                $${formatNum(i.subtotal)}
                            </td>
                        </tr>`;
                    });
                    html += `</table>`;
                    html += `<div style="display:flex;justify-content:space-between;padding:.75rem .25rem 0;
                                         border-top:1px solid #222;margin-top:.5rem;">
                        <span style="font-weight:700;color:#a78bfa;">Total</span>
                        <span style="font-size:1.1rem;font-weight:900;color:#c4b5fd;">
                            $${formatNum(data.total)}
                        </span>
                    </div>`;
                    body.innerHTML = html;
                })
                .catch(() => {
                    body.innerHTML = '<p style="color:#f87171;">Error al cargar ítems.</p>';
                });
            }

            document.getElementById('modal-resumen').style.display = 'flex';
        }

        function cerrarResumen() {
            document.getElementById('modal-resumen').style.display = 'none';
        }

        // ── Marcar entregado ──────────────────────────────────────
        async function entregarDelivery(orderId, btn) {
            if (!confirm('¿Confirmar entrega del pedido?')) return;
            try {
                const r = await fetch(`/deliveries/${orderId}/deliver`, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                });
                const data = await r.json();
                if (data.success) window.location.reload();
                else alert(data.error ?? 'Error al marcar como entregado.');
            } catch {
                alert('Error de conexión.');
            }
        }

        // ── Helpers ───────────────────────────────────────────────
        function escHtml(str) {
            return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;')
                .replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
        }
        function formatNum(n) {
            return Number(n).toLocaleString('es-AR', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }
    </script>
</x-app-layout>
