<div class="flex-1 flex flex-col h-full">
    <!-- Area pesan yang dapat di-scroll -->
    <div class="flex-1 overflow-y-auto p-4 space-y-4" id="message-container" wire:poll.visible.10s="loadMessages">
        @if (isset($messages) && count($messages) > 0)
            @foreach ($messages as $message)
                <div class="chat {{ $message['sender_id'] == auth()->id() ? 'chat-end' : 'chat-start' }}">
                    @if ($message['sender_id'] != auth()->id())
                        <div class="chat-image avatar">
                            <div class="w-10 rounded-full">
                                <img src="{{ $message['user']['avatar'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($message['user']['name']) }}"
                                    alt="{{ $message['user']['name'] }}">
                            </div>
                        </div>
                        <div class="chat-header text-xs opacity-75 mb-1">
                            {{ $message['user']['name'] }}
                        </div>
                    @endif

                    <div class="chat-bubble {{ $message['sender_id'] == auth()->id() ? 'chat-bubble-primary' : '' }}">
                        @if ($message['body'])
                            <p>{{ $message['body'] }}</p>
                        @endif

                        @if (isset($message['attachments']) && count($message['attachments']) > 0)
                            <div class="space-y-2">
                                @foreach ($message['attachments'] as $attachment)
                                    @if (Str::startsWith($attachment['file_type'], 'image'))
                                        <!-- WhatsApp Style Image Preview -->
                                        <div class="chat-image-preview">
                                            <a href="{{ Storage::url($attachment['file_path']) }}" target="_blank">
                                                <img src="{{ Storage::url($attachment['file_path']) }}"
                                                    alt="{{ $attachment['file_name'] }}">
                                            </a>
                                            <div class="flex justify-between items-center mt-1 text-xs opacity-75">
                                                <span>{{ $attachment['file_name'] }}</span>
                                                <a href="{{ Storage::url($attachment['file_path']) }}" download
                                                    class="btn btn-ghost btn-xs rounded-full">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @else
                                        <div class="chat-attachment">
                                            <div class="flex-shrink-0">
                                                @if (Str::startsWith($attachment['file_type'], 'video'))
                                                    <i class="fas fa-video text-lg"></i>
                                                @elseif(Str::startsWith($attachment['file_type'], 'audio'))
                                                    <i class="fas fa-music text-lg"></i>
                                                @elseif(Str::startsWith($attachment['file_type'], 'application/pdf'))
                                                    <i class="fas fa-file-pdf text-lg"></i>
                                                @elseif(Str::startsWith($attachment['file_type'], 'application/msword') ||
                                                        Str::startsWith($attachment['file_type'], 'application/vnd.openxmlformats-officedocument.wordprocessingml'))
                                                    <i class="fas fa-file-word text-lg"></i>
                                                @elseif(Str::startsWith($attachment['file_type'], 'application/vnd.ms-excel') ||
                                                        Str::startsWith($attachment['file_type'], 'application/vnd.openxmlformats-officedocument.spreadsheetml'))
                                                    <i class="fas fa-file-excel text-lg"></i>
                                                @else
                                                    <i class="fas fa-file text-lg"></i>
                                                @endif
                                            </div>
                                            <div class="flex-1">
                                                <a href="{{ Storage::url($attachment['file_path']) }}" target="_blank"
                                                    class="hover:underline font-medium">
                                                    {{ $attachment['file_name'] }}
                                                    <div class="text-xs opacity-75">
                                                        {{ round($attachment['file_size'] / 1024, 2) }} KB
                                                    </div>
                                                </a>
                                            </div>
                                            <a href="{{ Storage::url($attachment['file_path']) }}" download
                                                class="flex-shrink-0 btn btn-circle btn-ghost btn-xs">
                                                <i class="fas fa-download text-sm"></i>
                                            </a>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="chat-footer">
                        <span>{{ $message['created_at_formatted'] ?? \Carbon\Carbon::parse($message['created_at'])->setTimezone('Asia/Jakarta')->format('H:i') }}</span>
                        @if ($message['sender_id'] == auth()->id())
                            @if (isset($message['is_read']) && $message['is_read'])
                                <span class="text-primary">
                                    <i class="fas fa-check-double"></i>
                                </span>
                            @else
                                <span class="opacity-75">
                                    <i class="fas fa-check"></i>
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="flex items-center justify-center h-full">
                <div class="text-center opacity-50">
                    <p>Belum ada pesan. Kirim pesan pertama!</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Form input pesan yang tetap di bawah - Perbaikan dengan wire:submit.prevent -->
    <div class="chat-input-container">
        <form wire:submit.prevent="sendMessage" class="flex items-end gap-2">
            <div class="flex-1">
                @if (isset($attachments) && count($attachments) > 0)
                    <div class="mb-2 flex flex-wrap gap-2">
                        @foreach ($attachments as $index => $attachment)
                            <div class="badge badge-lg gap-2">
                                <div class="truncate max-w-[150px]">{{ $attachment->getClientOriginalName() }}</div>
                                <button type="button" wire:click="removeAttachment({{ $index }})"
                                    class="btn btn-xs btn-circle btn-ghost">✕</button>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="form-control">
                    <textarea wire:model="messageText" rows="1" placeholder="Ketik pesan..."
                        class="textarea textarea-bordered w-full resize-none rounded-full" data-auto-resize x-data
                        x-on:keydown.enter.prevent="if(!$event.shiftKey) { $wire.sendMessage(); }"></textarea>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <label for="attachment" class="btn btn-circle btn-ghost">
                    <i class="fas fa-paperclip text-lg"></i>
                    <input id="attachment" type="file" wire:model="attachments" multiple class="hidden">
                </label>

                <button type="submit" class="btn btn-circle btn-primary">
                    <i class="fas fa-paper-plane text-lg"></i>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Inisialisasi
            window.scrollMessagesToBottom('message-container');

            // Event handlers
            Livewire.on('messageAdded', (params) => {
                window.scrollMessagesToBottom(params.containerId || 'message-container');
                playMessageSound();
            });

            Livewire.on('messagesLoaded', () => {
                window.scrollMessagesToBottom('message-container');
            });

            Livewire.on('messageReceived', () => {
                console.log('New message notification received');
                Livewire.dispatch('$refresh');
                playMessageSound();
            });

            // Auto resize setup
            setupTextareaAutoResize();

            // Setup observer
            const messageContainer = document.getElementById('message-container');
            if (messageContainer) {
                const observer = new MutationObserver(() => {
                    window.scrollMessagesToBottom('message-container');
                });

                observer.observe(messageContainer, {
                    childList: true,
                    subtree: true
                });
            }
        });

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

        function playMessageSound() {
            // Implementasi sound notification (opsional)
            /* Contoh implementasi sederhana:
            const audio = new Audio('/notification.mp3');
            audio.volume = 0.5;
            audio.play().catch(e => console.log('Audio playback prevented:', e));
            */
        }
    </script>
@endpush
