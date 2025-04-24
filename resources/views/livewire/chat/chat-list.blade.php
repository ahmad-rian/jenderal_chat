<div class="h-full flex flex-col">
    <div class="p-4 border-b border-base-200">
        <div class="relative">
            <input type="text" placeholder="Cari percakapan..." wire:model.live="searchTerm"
                class="input input-bordered w-full pl-10 pr-4">
            <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-base-content/50">
                <i class="fas fa-search"></i>
            </div>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto">
        @if (isset($conversations) && $conversations->count() > 0)
            @foreach ($conversations as $conversation)
                <div wire:click="selectConversation({{ $conversation->id }})"
                    class="p-4 border-b border-base-200 hover:bg-base-200 cursor-pointer {{ $selectedConversationId == $conversation->id ? 'bg-base-200' : '' }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="relative">
                                @php
                                    $otherUser = $conversation->getOtherUser(auth()->id());
                                @endphp
                                <div class="avatar {{ $otherUser->is_online ? 'online' : 'offline' }}">
                                    <div class="w-12 rounded-full">
                                        @if ($otherUser->avatar)
                                            <img src="{{ $otherUser->avatar }}" alt="{{ $otherUser->name }}">
                                        @else
                                            <div
                                                class="bg-primary text-primary-content rounded-full flex items-center justify-center h-full">
                                                {{ substr($otherUser->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="font-medium text-base-content">
                                    {{ $otherUser->name }}
                                </div>
                                <div class="text-sm opacity-70 truncate w-40">
                                    @if ($conversation->latestMessage)
                                        @if ($conversation->latestMessage->sender_id == auth()->id())
                                            <span class="opacity-50">Anda: </span>
                                        @endif
                                        {{ $conversation->latestMessage->body ?? 'Lampiran' }}
                                    @else
                                        Belum ada pesan
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col items-end">
                            <div class="text-xs opacity-50">
                                @if ($conversation->latestMessage)
                                    {{ $conversation->latestMessage->created_at->diffForHumans(null, true) }}
                                @endif
                            </div>
                            @php
                                $unreadCount = $conversation
                                    ->messages()
                                    ->where('sender_id', '!=', auth()->id())
                                    ->where('is_read', false)
                                    ->count();
                            @endphp
                            @if ($unreadCount > 0)
                                <div class="badge badge-primary badge-sm">{{ $unreadCount }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="p-4 text-center opacity-50">
                Belum ada percakapan.
            </div>
        @endif
    </div>
</div>
