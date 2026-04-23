/**
 * Tables (Mesas) page controller.
 *
 * - Sin polling. Realtime 100% via _restaurantWS (Echo).
 * - Cachea elementos DOM en init() para evitar querySelector repetitivos.
 * - Expone globals (abrirMesa, verResumen, etc.) solo mientras la página está activa.
 * - Se limpia completamente en cada nav SPA via spaRegisterCleanup.
 */
(function () {
    'use strict';

    // ── Cache de elementos DOM ────────────────────────────────────────────
    let $ = {};

    function cacheDOM() {
        $.dotFree      = document.getElementById('dot-free');
        $.lblFree      = document.getElementById('lbl-free');
        $.lblOcc       = document.getElementById('lbl-occupied');
        $.wsIndicator  = document.getElementById('ws-indicator');
        $.wsLabel      = document.getElementById('ws-label');
        $.modalResumen = document.getElementById('modal-resumen');
        $.modalTitulo  = document.getElementById('modal-titulo');
        $.modalCont    = document.getElementById('modal-contenido');
        $.modalBtnAbrir= document.getElementById('modal-btn-abrir');
    }

    // ── Estado WebSocket (indicador visual) ──────────────────────────────
    const WS_STATES = {
        connected:    { bg: '#10b981', text: 'En vivo',       color: '#34d399' },
        disconnected: { bg: '#ef4444', text: 'Desconectado',  color: '#f87171' },
        connecting:   { bg: '#d97706', text: 'Conectando\u2026', color: '#fbbf24' },
    };

    function onWsState(e) {
        const s = WS_STATES[e.detail];
        if (!s || !$.wsIndicator) return;
        $.wsIndicator.style.background = s.bg;
        $.wsLabel.textContent          = s.text;
        $.wsLabel.style.color          = s.color;
    }

    // ── Helpers de estado de mesas ───────────────────────────────────────
    function _applyMesaState(num, cls, title, ks, dotBg, dotAnim, estadoTxt, showBtn) {
        const btn = document.getElementById('mesa-btn-' + num);
        if (!btn) return;
        btn.className             = 'mesa-btn ' + cls;
        btn.title                 = title;
        btn.dataset.kitchenStatus = ks;

        const dot = document.getElementById('mesa-dot-' + num);
        if (dot) { dot.style.background = dotBg; dot.style.animation = dotAnim; }

        const lbl = document.getElementById('mesa-estado-' + num);
        if (lbl) lbl.textContent = estadoTxt;

        const ent = document.getElementById('btn-entregar-' + num);
        if (ent) ent.classList.toggle('btn-entregar-hidden', !showBtn);

        recalcContadores();
    }

    function setMesaLibre(num) {
        _applyMesaState(num, 'mesa-libre', `Mesa ${num} \u2014 Libre`,
            'pendiente', '#10b981', 'pulse 2s infinite', 'libre', false);
    }
    function setMesaOcupada(num) {
        _applyMesaState(num, 'mesa-ocupada', `Mesa ${num} \u2014 Ocupada`,
            'pendiente', '#ef4444', '', 'ocupada', false);
    }
    function setMesaLista(num) {
        _applyMesaState(num, 'mesa-lista', `Mesa ${num} \u2014 Listo para entregar`,
            'listo', '#eab308', '', 'listo', true);
    }

    // ── Contadores header (usa longitud de NodeList en lugar de count vars) ──
    function recalcContadores() {
        const total    = document.querySelectorAll('.mesa-btn').length;
        const occupied = document.querySelectorAll('.mesa-ocupada,.mesa-lista').length;
        if ($.lblFree) $.lblFree.textContent = `${total - occupied} libres`;
        if ($.lblOcc)  $.lblOcc.textContent  = `${occupied} ocupadas`;
    }

    // ── Handler de eventos WebSocket ─────────────────────────────────────
    function handleOrderUpdate(data) {
        const num = data.table_number;
        if (!num) return;
        const card = document.getElementById('mesa-btn-' + num);
        if (!card) return;

        if (data.order_id) card.dataset.orderId = data.order_id;

        if (data.action === 'closed' || data.action === 'cancelled') {
            setMesaLibre(num);
        } else if (data.action === 'updated') {
            if (data.kitchen_status === 'listo') setMesaLista(num);
            else setMesaOcupada(num);
        }
    }

    // ── Entregar pedido ──────────────────────────────────────────────────
    function entregarPedido(num, btn) {
        const card    = document.getElementById('mesa-btn-' + num);
        const orderId = card && card.dataset.orderId;
        if (!orderId) return;
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        btn.disabled = true; btn.textContent = '\u2026';
        fetch('/orders/' + orderId + '/deliver', {
            method: 'PATCH',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) setMesaOcupada(num);
            else { btn.disabled = false; btn.textContent = 'Pedido entregado'; }
        })
        .catch(function () { btn.disabled = false; btn.textContent = 'Pedido entregado'; });
    }

    // ── Modal de resumen rápido ──────────────────────────────────────────
    function verResumen(numero, e) {
        e.preventDefault(); e.stopPropagation();
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        if ($.modalTitulo)   $.modalTitulo.textContent = 'Mesa ' + numero;
        if ($.modalCont)     $.modalCont.innerHTML = '<p style="color:#6b7280;">Cargando...</p>';
        if ($.modalBtnAbrir) $.modalBtnAbrir.onclick = function () { abrirMesa(numero); };
        if ($.modalResumen)  $.modalResumen.style.display = 'flex';

        fetch('/api/tables/' + numero + '/summary', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (!$.modalCont) return;
            if (!data.success) {
                $.modalCont.innerHTML = '<p style="color:#6b7280;font-style:italic;">' + data.message + '</p>';
                return;
            }
            let html = '<table style="width:100%;border-collapse:collapse;margin-bottom:.75rem;">'
                + '<thead><tr style="border-bottom:1px solid #333;">'
                + '<th style="text-align:left;padding-bottom:.5rem;color:#6b7280;font-weight:500;font-size:.75rem;">Producto</th>'
                + '<th style="text-align:center;padding-bottom:.5rem;color:#6b7280;font-weight:500;font-size:.75rem;">Cant.</th>'
                + '<th style="text-align:right;padding-bottom:.5rem;color:#6b7280;font-weight:500;font-size:.75rem;">Subtotal</th>'
                + '</tr></thead><tbody>';
            data.items.forEach(function (item) {
                html += '<tr style="border-bottom:1px solid #1f1f1f;">'
                    + '<td style="padding:.4rem 0;color:#e5e7eb;">' + item.name + '</td>'
                    + '<td style="padding:.4rem 0;text-align:center;color:#9ca3af;">' + item.quantity + '</td>'
                    + '<td style="padding:.4rem 0;text-align:right;color:#4ade80;">$'
                    + parseFloat(item.subtotal).toLocaleString('es-AR') + '</td>'
                    + '</tr>';
            });
            html += '</tbody></table>'
                + '<div style="text-align:right;font-weight:800;color:#fbbf24;font-size:1rem;">'
                + 'Total: $' + parseFloat(data.total).toLocaleString('es-AR') + '</div>';
            $.modalCont.innerHTML = html;
        })
        .catch(function () {
            if ($.modalCont) $.modalCont.innerHTML = '<p style="color:#f87171;">Error al cargar el resumen.</p>';
        });
    }

    function cerrarModal() {
        if ($.modalResumen) $.modalResumen.style.display = 'none';
    }

    function abrirMesa(numero) {
        window.location.href = '/tables/' + numero + '/order';
    }

    // ── Handlers de evento DOM ───────────────────────────────────────────
    function onKeydown(e) { if (e.key === 'Escape') cerrarModal(); }

    // ── Init ─────────────────────────────────────────────────────────────
    function init() {
        cacheDOM();

        // Registrar handler WS y mostrar estado actual
        if (window._restaurantWS) {
            window._restaurantWS.on('tables', handleOrderUpdate);
            var currentState = window._restaurantWS.state();
            if (currentState !== 'unknown') {
                document.dispatchEvent(new CustomEvent('ws:state', { detail: currentState }));
            }
        }

        document.addEventListener('ws:state', onWsState);
        document.addEventListener('keydown',   onKeydown);

        // Exponer globals que los onclick del Blade necesitan
        window.abrirMesa      = abrirMesa;
        window.verResumen     = verResumen;
        window.cerrarModal    = cerrarModal;
        window.entregarPedido = entregarPedido;

        // Registrar limpieza SPA
        if (typeof window.spaRegisterCleanup === 'function') {
            window.spaRegisterCleanup(function () {
                if (window._restaurantWS) window._restaurantWS.off('tables');
                document.removeEventListener('ws:state', onWsState);
                document.removeEventListener('keydown',  onKeydown);
                delete window.abrirMesa;
                delete window.verResumen;
                delete window.cerrarModal;
                delete window.entregarPedido;
                $ = {}; // liberar refs DOM
            });
        }
    }

    window.TablesPage = { init: init };
})();
