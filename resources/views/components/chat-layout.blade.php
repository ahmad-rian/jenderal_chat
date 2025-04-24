<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ auth()->user()->theme ?? 'light' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">
    <title>{{ config('app.name', 'Laravel') }} </title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.4.24/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
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

        .chat-attachment {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            background-color: rgba(229, 231, 235, 0.5);
            border-radius: 0.5rem;
            margin-top: 0.5rem;
        }

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

        textarea[data-auto-resize] {
            min-height: 2.5rem;
            max-height: 120px;
            overflow-y: hidden;
            resize: none;
            transition: height 0.1s ease;
        }

        .dropdown-content {
            z-index: 1000 !important;
        }

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
        <x-layouts.chat-navigation />
        <main class="flex-1 overflow-hidden">
            {{ $slot }}
        </main>
    </div>

    <script src="https://js.pusher.com/8.0/pusher.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            updateOnlineStatus();
            setInterval(updateOnlineStatus, 60000);

            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'visible') {
                    updateOnlineStatus();
                }
            });

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

            setupTextareaAutoResize();
            setupMessageObserver();
        });

        function updateOnlineStatus() {
            fetch('/user/status/online', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).catch(error => console.error('Error updating status:', error));
        }

        function setupTextareaAutoResize() {
            document.querySelectorAll('textarea[data-auto-resize]').forEach(textarea => {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
                });

                if (textarea.value) {
                    textarea.dispatchEvent(new Event('input'));
                }
            });
        }

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

        window.addEventListener('beforeunload', function() {
            navigator.sendBeacon('/user/status/offline', JSON.stringify({
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }));
        });

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('messageAdded', (params) => {
                window.scrollMessagesToBottom(params.containerId || 'message-container');
            });

            Livewire.on('messagesLoaded', () => {
                window.scrollMessagesToBottom('message-container');
            });

            Livewire.on('focusMessageInput', () => {
                setTimeout(() => {
                    const textarea = document.querySelector('textarea[wire\\:model="messageText"]');
                    if (textarea) {
                        textarea.focus();
                    }
                }, 100);
            });

            Livewire.on('conversationSelected', (conversationId) => {
                if (window.innerWidth < 768) {
                    const sidebar = document.querySelector('.chat-sidebar');
                    const main = document.querySelector('.chat-main');

                    if (sidebar && main) {
                        sidebar.classList.add('md-hidden');
                        main.classList.remove('md-hidden');
                    }
                }

                setTimeout(() => {
                    window.scrollMessagesToBottom('message-container');
                }, 300);
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            window.Echo.private(`conversation.${conversationId}`)
                .listen('.NewMessageSent', (e) => {
                    Livewire.dispatch('messageReceived');
                })
                .listen('.MessageRead', (e) => {
                    Livewire.dispatch('messageRead', e);
                });

            window.Echo.connector.pusher.connection.bind('connected', () => {
                console.log('Pusher connected successfully!');
            });

            window.Echo.connector.pusher.connection.bind('error', (err) => {
                console.error('Pusher connection error:', err);
            });
        });

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
