import './bootstrap';
import Alpine from 'alpinejs';
import { Livewire, Alpine as LivewireAlpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

window.Alpine = Alpine;
LivewireAlpine.start();
Alpine.start();
Livewire.start();

document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible') {
        fetch('/user/status/online', { // Menghapus prefix /api
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    fetch('/user/status/online', { // Menghapus prefix /api
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    });
});

window.addEventListener('beforeunload', function() {
    navigator.sendBeacon('/user/status/offline', JSON.stringify({ // Menghapus prefix /api
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }));
});