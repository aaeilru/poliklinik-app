import './bootstrap';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Pusher dipakai sebagai driver oleh Echo (compatible dengan Reverb)
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',

    // Nilai diambil dari .env saat build Vite
    key:     import.meta.env.VITE_REVERB_APP_KEY,
    wsHost:  import.meta.env.VITE_REVERB_HOST  ?? 'localhost',
    wsPort:  import.meta.env.VITE_REVERB_PORT  ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT  ?? 443,

    // false = HTTP, true = HTTPS
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',

    enabledTransports: ['ws', 'wss'],
});

// Log status koneksi di DevTools Console browser
window.Echo.connector.pusher.connection.bind('connected', () => {
    console.log('[Reverb] WebSocket terhubung!');
});

window.Echo.connector.pusher.connection.bind('error', (err) => {
    console.warn('[Reverb] Gagal konek, fallback ke polling.', err);
});