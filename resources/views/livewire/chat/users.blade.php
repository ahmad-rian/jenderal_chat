<x-chat-layout>
    <div class="container mx-auto py-4 px-4 md:px-0">
        <div class="card max-w-2xl mx-auto bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">Mulai Percakapan Baru</h2>
                <p class="text-base-content/70 text-sm">Pilih pengguna untuk memulai percakapan</p>

                <div class="divider"></div>

                <div class="form-control">
                    <div class="input-group">
                        <input type="text" id="search-users" placeholder="Cari pengguna..."
                            class="input input-bordered w-full" />
                        <button class="btn btn-square">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="overflow-y-auto max-h-[500px] mt-4">
                    @forelse($users as $user)
                        <div class="user-item cursor-pointer hover:bg-base-200 transition-colors rounded-lg p-3 mb-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="avatar {{ $user->is_online ? 'online' : 'offline' }}">
                                        <div class="w-12 rounded-full">
                                            @if ($user->avatar)
                                                <img src="{{ $user->avatar }}" alt="{{ $user->name }}">
                                            @else
                                                <div
                                                    class="bg-primary text-primary-content w-12 h-12 rounded-full flex items-center justify-center">
                                                    {{ $user->initials() }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-medium">
                                            {{ $user->name }}
                                        </div>
                                        <div class="text-xs text-base-content/70">
                                            {{ $user->is_online ? 'Online' : ($user->last_active_at ? 'Terakhir online ' . \Carbon\Carbon::parse($user->last_active_at)->diffForHumans() : 'Offline') }}
                                        </div>
                                    </div>
                                </div>
                                <form action="{{ route('chat.create', $user) }}" method="POST" class="chat-form">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                            </path>
                                        </svg>
                                        Chat
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info">
                            <svg class="h-6 w-6 stroke-current shrink-0" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Tidak ada pengguna lain yang tersedia.</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tambahkan click handler pada seluruh area user
            const userItems = document.querySelectorAll('.user-item');
            userItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    // Jika yang diklik bukan tombol atau form, kirim form
                    if (!e.target.closest('button') && !e.target.closest('form')) {
                        const form = this.querySelector('form');
                        if (form) form.submit();
                    }
                });
            });

            // Debugging untuk form submit
            const chatForms = document.querySelectorAll('.chat-form');
            chatForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    console.log('Form submitted:', this.action);
                });
            });
        });
    </script>
</x-chat-layout>
