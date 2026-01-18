import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

const csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content;

const scheme = import.meta.env.VITE_REVERB_SCHEME
    ?? (window.location.protocol === 'https:' ? 'https' : 'http');

const host = import.meta.env.VITE_REVERB_HOST
    ?? window.location.hostname;

const port = Number(import.meta.env.VITE_REVERB_PORT
    ?? 8080);

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: host,
    wsPort: port,
    wssPort: port,
    forceTLS: scheme === 'https',
    enabledTransports: ['ws', 'wss'],
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {},
    },
});
