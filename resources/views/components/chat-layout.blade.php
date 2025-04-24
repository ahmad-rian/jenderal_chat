<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ auth()->user()->theme ?? 'light' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">
    <title>{{ config('app.name', 'Laravel') }} - Chat</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- DaisyUI & TailwindCSS -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.4.24/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- CSS Kustom untuk Chat dengan Fixed Layout -->
    <style>
        /* Style untuk chat container dan elemen-elemennya */
        body {
            overflow: hidden;
            height: 100vh;
        }

        .chat-container {
            height: calc(100vh - 64px);
            display: flex;
            overflow: hidden;
            background-color: white;
        }

        .chat-sidebar {
            width: 300px;
            background-color: white;
            border-right: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
        }

        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
            position: relative;
            background-color: white;
        }

        .chat-header {
            background-color: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 30;
        }

        #message-container {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
            scroll-behavior: smooth;
            background-color: white;
        }

        .chat-input-container {
            background-color: white;
            border-top: 1px solid #e5e7eb;
            padding: 1rem;
            z-index: 20;
        }

        /* Style untuk chat bubbles */
        .chat {
            margin-bottom: 1rem;
        }

        .chat-image {
            margin-bottom: 0.25rem;
        }

        .chat-bubble {
            display: inline-block;
            padding: 0.75rem 1rem;
            border-radius: 1rem;
            max-width: 80%;
            word-break: break-word;
        }

        .chat-start .chat-bubble {
            background-color: #e5e7eb;
            color: #1f2937;
            border-bottom-left-radius: 0.25rem;
        }

        .chat-end .chat-bubble {
            background-color: #3b82f6;
            color: white;
            border-bottom-right-radius: 0.25rem;
        }

        .chat-end {
            text-align: right;
        }

        .chat-footer {
            font-size: 0.75rem;
            opacity: 0.7;
            margin-top: 0.25rem;
        }

        /* Style untuk gambar di chat */
        .chat-image-preview {
            margin-top: 0.5rem;
            border-radius: 0.5rem;
            overflow: hidden;
            max-width: 240px;
        }

        .chat-image-preview img {
            width: 100%;
            height: auto;
        }

        /* Style untuk lampiran file di chat */
        .chat-attachment {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            background-color: rgba(229, 231, 235, 0.5);
            border-radius: 0.5rem;
            margin-top: 0.5rem;
        }

        /* Style untuk avatar online/offline */
        .avatar.online:before {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 0.75rem;
            height: 0.75rem;
            background-color: #10b981;
            border-radius: 50%;
            border: 2px solid white;
        }

        .avatar.offline:before {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 0.75rem;
            height: 0.75rem;
            background-color: #9ca3af;
            border-radius: 50%;
            border: 2px solid white;
        }

        /* Style untuk auto-resize textarea */
        textarea[data-auto-resize] {
            min-height: 2.5rem;
            max-height: 120px;
            overflow-y: hidden;
            resize: none;
            transition: height 0.1s ease;
        }

        /* Style untuk dropdown yang tertutup */
        .dropdown-content {
            z-index: 1000 !important;
        }

        /* Style untuk mobile view */
        @media (max-width: 767px) {
            .chat-sidebar {
                width: 100%;
            }

            .md-hidden {
                display: none;
            }
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-base-200">
    <div class="min-h-screen flex flex-col">
        <!-- Navbar -->
        <x-layouts.chat-navigation />

        <!-- Main Content -->
        <main class="flex-1 overflow-hidden">
            {{ $slot }}
        </main>
    </div>

    <!-- Pusher Script -->
    <script src="https://js.pusher.com/8.0/pusher.min.js"></script>

    <!-- Script Kustom -->
    <script>
        // Status online handler
        document.addEventListener('DOMContentLoaded', function() {
            // Update online status
            updateOnlineStatus();

            // Set interval untuk update status setiap 60 detik
            setInterval(updateOnlineStatus, 60000);

            // Event listener untuk visibility change
            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'visible') {
                    updateOnlineStatus();
                }
            });

            // Helper function untuk scroll messages container
            window.scrollMessagesToBottom = function(containerId, smooth = true) {
                setTimeout(() => {
                    const container = document.getElementById(containerId);
                    if (container) {
                        if (smooth) {
                            container.scrollTo({
                                top: container.scrollHeight,
                                behavior: 'smooth'
                            });
                        } else {
                            container.scrollTop = container.scrollHeight;
                        }
                    }
                }, 100);
            };

            // Setup auto-resize untuk textarea
            setupTextareaAutoResize();

            // Tambahkan mutation observer untuk container pesan
            setupMessageObserver();
        });

        // Fungsi untuk update status online
        function updateOnlineStatus() {
            fetch('/user/status/online', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).catch(error => console.error('Error updating status:', error));
        }

        // Fungsi untuk setup auto-resize textarea
        function setupTextareaAutoResize() {
            document.querySelectorAll('textarea[data-auto-resize]').forEach(textarea => {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
                });

                // Initially set height
                if (textarea.value) {
                    textarea.dispatchEvent(new Event('input'));
                }
            });
        }

        // Fungsi untuk setup message observer
        function setupMessageObserver() {
            const containers = document.querySelectorAll('.messages-container, #message-container');
            containers.forEach(container => {
                if (container) {
                    const observer = new MutationObserver(() => {
                        window.scrollMessagesToBottom(container.id);
                    });

                    observer.observe(container, {
                        childList: true,
                        subtree: true
                    });
                }
            });
        }

        // Listener untuk event beforeunload
        window.addEventListener('beforeunload', function() {
            navigator.sendBeacon('/user/status/offline', JSON.stringify({
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }));
        });

        // Register custom Livewire hooks
        document.addEventListener('livewire:initialized', () => {
            // Hook untuk auto-scroll messages
            Livewire.on('messageAdded', (params) => {
                window.scrollMessagesToBottom(params.containerId || 'message-container');
            });

            Livewire.on('messagesLoaded', () => {
                window.scrollMessagesToBottom('message-container');
            });

            // Hook untuk focus input setelah kirim pesan
            Livewire.on('focusMessageInput', () => {
                setTimeout(() => {
                    const textarea = document.querySelector('textarea[wire\\:model="messageText"]');
                    if (textarea) {
                        textarea.focus();
                    }
                }, 100);
            });

            // Hook untuk conversation selected
            Livewire.on('conversationSelected', (conversationId) => {
                console.log('Conversation selected:', conversationId);

                // Untuk mobile view
                if (window.innerWidth < 768) {
                    const sidebar = document.querySelector('.chat-sidebar');
                    const main = document.querySelector('.chat-main');

                    if (sidebar && main) {
                        sidebar.classList.add('md-hidden');
                        main.classList.remove('md-hidden');
                    }
                }

                // Trigger scroll ke bawah setelah konten dimuat
                setTimeout(() => {
                    window.scrollMessagesToBottom('message-container');
                }, 300);
            });
        });

        // Pastikan Pusher diinisialisasi dengan benar
        document.addEventListener('DOMContentLoaded', function() {
            window.Echo.private(`conversation.${conversationId}`)
                .listen('.NewMessageSent', (e) => {
                    console.log('New message received via Pusher:', e);
                    Livewire.dispatch('messageReceived');
                })
                .listen('.MessageRead', (e) => {
                    console.log('Message read event received via Pusher:', e);
                    Livewire.dispatch('messageRead', e);
                });

            // Debugging Pusher
            window.Echo.connector.pusher.connection.bind('connected', () => {
                console.log('Pusher connected successfully!');
                console.log('Socket ID:', window.Echo.socketId());
            });

            window.Echo.connector.pusher.connection.bind('error', (err) => {
                console.error('Pusher connection error:', err);
            });
        });

        // Helper function to check Pusher connection
        window.checkPusherConnection = function() {
            if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
                return window.Echo.connector.pusher.connection.state;
            }
            return 'Not initialized';
        };
    </script>

    @stack('scripts')
    @livewireScripts
</body>

</html>
