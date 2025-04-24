import './bootstrap';
import './chat.js';  
import Alpine from 'alpinejs';
import { Livewire, Alpine as LivewireAlpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

// Inisialisasi Pusher dan Echo secara benar
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Coba tangani error jika Echo gagal diinisialisasi
try {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: import.meta.env.VITE_PUSHER_APP_KEY || process.env.MIX_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || process.env.MIX_PUSHER_APP_CLUSTER,
        forceTLS: true,
        encrypted: true,
        enabledTransports: ['ws', 'wss'],
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
        },
    });
    
    console.log('Laravel Echo initialized successfully');
    
    // Aktifkan logging pusher untuk debugging
    window.Echo.connector.pusher.config.enableLogging = true;
    
} catch (error) {
    console.error('Failed to initialize Laravel Echo:', error);
    // Buat Echo dummy untuk mencegah error "Echo is not defined"
    window.Echo = {
        private: () => ({
            listen: () => ({}),
        }),
        channel: () => ({
            listen: () => ({}),
        }),
        connector: {
            pusher: { 
                connection: { 
                    state: 'failed-to-initialize'
                }
            }
        }
    };
}

window.Alpine = Alpine;
LivewireAlpine.start();
Alpine.start();
Livewire.start();

// Status online handler
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible') {
        fetch('/user/status/online', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).catch(error => console.warn('Error updating online status:', error));
    }
});

document.addEventListener('DOMContentLoaded', function() {
    fetch('/user/status/online', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }).catch(error => console.warn('Error updating online status:', error));
});

window.addEventListener('beforeunload', function() {
    navigator.sendBeacon('/user/status/offline', JSON.stringify({
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }));
});