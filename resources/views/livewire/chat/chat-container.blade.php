<div>
    <div class="chat-container" data-theme="{{ $theme }}">
        <!-- Sidebar -->
        <div class="chat-sidebar {{ $isMobileView ? 'hidden md:flex' : 'flex' }}">
            <!-- Conversation List -->
            <div class="flex-1 overflow-y-auto">
                <livewire:chat.chat-list :conversation-ids="$conversations->pluck('id')" :wire:key="'chat-list-'.now()" />
            </div>
        </div>

        <!-- Chat Area -->
        <div class="chat-main {{ !$isMobileView ? 'hidden md:flex' : 'flex' }}">
            @if ($selectedConversation)
                <!-- Chat Header -->
                <div class="chat-header" wire:key="chat-header-{{ $selectedConversation->id }}"
                    data-conversation-id="{{ $selectedConversation->id }}">
                    <div class="flex items-center">
                        <button class="btn btn-ghost btn-circle md:hidden mr-2" wire:click="toggleMobileView(false)">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <div
                            class="avatar {{ $selectedConversation->getOtherUser(auth()->id())->is_online ? 'online' : 'offline' }}">
                            <div class="w-10 rounded-full">
                                @if ($selectedConversation->getOtherUser(auth()->id())->avatar)
                                    <img src="{{ $selectedConversation->getOtherUser(auth()->id())->avatar }}"
                                        alt="Avatar" />
                                @else
                                    <div
                                        class="bg-primary text-primary-content w-10 h-10 rounded-full flex items-center justify-center">
                                        {{ substr($selectedConversation->getOtherUser(auth()->id())->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="ml-3">
                            <h3 class="font-medium">{{ $selectedConversation->getOtherUser(auth()->id())->name }}</h3>
                            <livewire:chat.user-status :userId="$selectedConversation->getOtherUser(auth()->id())->id"
                                :wire:key="'user-status-'.$selectedConversation->getOtherUser(auth()->id())->id" />
                        </div>
                    </div>
                    <div class="dropdown dropdown-end">
                        <label tabindex="0" class="btn btn-ghost btn-circle">
                            <i class="fas fa-ellipsis-vertical"></i>
                        </label>
                        <ul tabindex="0"
                            class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52 z-[1000]">
                            <li><a href="#">Lihat profil</a></li>
                            <li><a href="#">Hapus chat</a></li>
                            <li><a href="#">Blokir</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Messages Area -->
                <div class="flex-1 overflow-hidden" wire:key="message-wrapper-{{ $selectedConversation->id }}"
                    data-conversation-id="{{ $selectedConversation->id }}">
                    <livewire:chat.chat-messages :conversation="$selectedConversation"
                        :wire:key="'chat-messages-' . $selectedConversation->id" />
                </div>
            @else
                <div class="flex-1 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-comments text-6xl text-base-content opacity-20 mb-4"></i>
                        <p class="text-lg">Pilih percakapan untuk mulai chatting</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Debugging Tools untuk troubleshooting realtime - bisa dihapus di production -->
    {{-- <div class="hidden">
        <div id="debug-info">
            <p>Selected Conversation ID: {{ $selectedConversation ? $selectedConversation->id : 'None' }}</p>
            <p>Mobile View: {{ $isMobileView ? 'Yes' : 'No' }}</p>
            <p>Pusher Status: <span id="pusher-status">Checking...</span></p>
        </div>
    </div> --}}
</div>
@push('scripts')
    <script>
        // Inisialisasi Echo setelah DOM siap
        document.addEventListener('livewire:initialized', () => {
            // Hanya set up Pusher jika ada percakapan terpilih
            if (@js($selectedConversation ? $selectedConversation->id : null)) {
                const conversationId = @js($selectedConversation ? $selectedConversation->id : null);

                console.log(`Setting up Pusher for conversation.${conversationId}`);

                // Dengarkan event private channel
                try {
                    window.Echo.private(`conversation.${conversationId}`)
                        .listen('.NewMessageSent', (e) => {
                            console.log('New message received via Pusher:', e);
                            Livewire.dispatch('messageReceived');
                        })
                        .listen('.MessageRead', (e) => {
                            console.log('Message read status received via Pusher:', e);
                            Livewire.dispatch('messageRead', e);
                        });
                } catch (error) {
                    console.error('Error setting up Pusher listeners:', error);
                }
            }

            // Handle mobile view untuk responsivitas
            Livewire.on('mobileViewToggled', (isShowingChat) => {
                if (window.innerWidth < 768) {
                    const sidebar = document.querySelector('.chat-sidebar');
                    const main = document.querySelector('.chat-main');

                    if (sidebar && main) {
                        if (isShowingChat) {
                            sidebar.classList.add('hidden');
                            main.classList.remove('hidden');
                        } else {
                            sidebar.classList.remove('hidden');
                            main.classList.add('hidden');
                        }
                    }
                }
            });

            // Monitoring Pusher connection
            setInterval(() => {
                if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
                    document.getElementById('pusher-status').textContent =
                        window.Echo.connector.pusher.connection.state;
                }
            }, 5000);
        });

        // Cek koneksi Pusher function untuk debugging
        window.checkPusherConnection = function() {
            if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
                console.log('Pusher connection state:', window.Echo.connector.pusher.connection.state);
                console.log('Socket ID:', window.Echo.socketId());
                return window.Echo.connector.pusher.connection.state === 'connected';
            }
            return false;
        };
    </script>
@endpush
