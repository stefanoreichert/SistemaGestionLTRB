import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;

const _csrfMeta = document.querySelector('meta[name="csrf-token"]');
if (_csrfMeta) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = _csrfMeta.getAttribute('content');
}

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

const reverbKey = import.meta.env.VITE_REVERB_APP_KEY;
const reverbHost = import.meta.env.VITE_REVERB_HOST || window.location.hostname;
const reverbScheme = import.meta.env.VITE_REVERB_SCHEME ?? (window.location.protocol === 'https:' ? 'https' : 'http');
const reverbPort = import.meta.env.VITE_REVERB_PORT ?? (reverbScheme === 'https' ? 443 : 80);

window.__echoUnavailable = !reverbKey;

if (!window.__echoUnavailable) {
    try {
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: reverbKey,
            wsHost: reverbHost,
            wsPort: reverbPort,
            wssPort: reverbPort,
            forceTLS: reverbScheme === 'https',
            enabledTransports: ['ws', 'wss'],
            disableStats: true,
        });
    } catch (error) {
        window.__echoUnavailable = true;
        console.error('[reverb] Failed to initialize Echo.', error);
    }
} else {
    console.warn('[reverb] Missing VITE_REVERB_APP_KEY. WebSockets disabled.');
}
