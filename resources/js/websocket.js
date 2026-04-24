/**
 * Global Restaurant WebSocket manager.
 *
 * - Se inicializa UNA SOLA VEZ al cargar app.js.
 * - Mantiene la suscripción al canal 'restaurant' durante toda la sesión SPA.
 * - Cada vista registra / desregistra su handler sin tocar el canal.
 * - Emite eventos DOM 'ws:state' con el estado de conexión.
 */
(function () {
    'use strict';

    if (window._restaurantWS) return; // singleton guard

    const handlers = {};

    function dispatch(state) {
        document.dispatchEvent(new CustomEvent('ws:state', { detail: state }));
    }

    function connect() {
        if (window.__echoUnavailable) return;
        if (!window.Echo) { setTimeout(connect, 100); return; }

        window.Echo.channel('restaurant')
            .listen('.order.updated', function (data) {
                const key = data.is_delivery ? 'delivery' : 'tables';
                if (typeof handlers[key] === 'function') handlers[key](data);
            });

        const conn = window.Echo.connector.pusher.connection;
        conn.bind('connected',    () => dispatch('connected'));
        conn.bind('disconnected', () => dispatch('disconnected'));
        conn.bind('connecting',   () => dispatch('connecting'));
    }

    connect();

    window._restaurantWS = {
        /** Registrar handler para una sección. key = 'tables' | 'delivery' */
        on:    function (key, fn) { handlers[key] = fn; },
        /** Desregistrar handler */
        off:   function (key)     { delete handlers[key]; },
        /** Estado actual de la conexión */
        state: function () {
            try { return window.Echo.connector.pusher.connection.state; }
            catch (e) { return 'unknown'; }
        },
    };
})();
