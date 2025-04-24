<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class ChatList extends Component
{
    public $conversationIds = [];
    public $selectedConversationId;
    public $searchTerm = '';

    protected $listeners = [
        'echo-private:conversation.*,NewMessageSent' => 'handlePusherMessage',
        'echo-private:conversation.*,MessageRead' => 'handlePusherMessageRead',
        'echo:user-status,UserOnlineStatusChanged' => 'refreshList',
        'chatListRefresh' => 'refreshList',
        'conversationSelected' => 'setSelectedConversation'
    ];

    public function mount($conversationIds)
    {
        $this->conversationIds = $conversationIds;
    }

    public function handlePusherMessage($event)
    {
        Log::info('New message via Pusher received in ChatList', ['event' => $event]);

        // Pastikan event untuk conversation yang terdaftar
        if (
            isset($event['message']['conversation_id']) &&
            in_array($event['message']['conversation_id'], $this->conversationIds)
        ) {

            // Refresh list secara realtime
            $this->refreshList();
        }
    }

    public function handlePusherMessageRead($event)
    {
        Log::info('Message read event received in ChatList', ['event' => $event]);

        if (
            isset($event['conversation_id']) &&
            in_array($event['conversation_id'], $this->conversationIds)
        ) {

            // Refresh list untuk update badge unread
            $this->refreshList();
        }
    }

    public function refreshList()
    {
        Log::info("Refreshing chat list...");
        $this->dispatch('$refresh');
    }

    public function setSelectedConversation($conversationId)
    {
        $this->selectedConversationId = $conversationId;
    }

    public function selectConversation($conversationId)
    {
        Log::info("Selecting conversation: $conversationId");
        $this->selectedConversationId = $conversationId;
        $this->dispatch('conversationSelected', $conversationId);
    }

    public function render()
    {
        // Get conversations with latest message and loaded relationships
        $conversations = Conversation::whereIn('id', $this->conversationIds)
            ->with(['sender', 'receiver', 'latestMessage.attachments'])
            ->latest('last_message_at')
            ->get();

        // Filter by search term if provided
        if (!empty($this->searchTerm)) {
            $searchTerm = '%' . $this->searchTerm . '%';
            $userId = auth()->id();

            $conversations = $conversations->filter(function ($conversation) use ($searchTerm, $userId) {
                $otherUser = $conversation->getOtherUser($userId);
                // Search in user name
                if (stripos($otherUser->name, $this->searchTerm) !== false) {
                    return true;
                }

                // Search in messages
                if ($conversation->latestMessage && stripos($conversation->latestMessage->body, $this->searchTerm) !== false) {
                    return true;
                }

                return false;
            });
        }

        return view('livewire.chat.chat-list', [
            'conversations' => $conversations,
            'selectedConversationId' => $this->selectedConversationId
        ]);
    }
}
