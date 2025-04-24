<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class ChatContainer extends Component
{
    public $selectedConversation;
    public $selectedUserId;
    public $conversations;
    public $searchTerm = '';
    public $isMobileView = false;
    public $theme;

    protected $listeners = [
        'conversationSelected' => 'loadConversation',
        'refreshConversations' => 'refreshConversations',
        'messageAdded' => 'handleNewMessage',
        'echo-private:conversation.*,NewMessageSent' => 'handleNewMessageBroadcast',
        'toggleMobileView' => 'toggleMobileView',
        'themeChanged' => 'updateTheme'
    ];

    public function mount($selectedConversation = null)
    {
        // Set mobile view based on user agent
        $this->isMobileView = $this->checkMobileDevice();

        // Get user theme preference
        $this->theme = auth()->user()->theme ?? 'light';

        // Load conversations first
        $this->loadConversations();

        if ($selectedConversation) {
            $this->selectedConversation = $selectedConversation;
            $this->selectedUserId = $selectedConversation->getOtherUser(auth()->id())->id;
        } elseif ($this->conversations && $this->conversations->count() > 0) {
            // Auto-select first conversation if none selected
            $this->selectedConversation = $this->conversations->first();
            $this->selectedUserId = $this->selectedConversation->getOtherUser(auth()->id())->id;
        }
    }

    /**
     * Handle broadcasts for new messages
     */
    public function handleNewMessageBroadcast($event)
    {
        Log::info('New message broadcast received in ChatContainer');

        // Only refresh if message belongs to a loaded conversation
        if (
            isset($event['message']['conversation_id']) &&
            $this->conversations->pluck('id')->contains($event['message']['conversation_id'])
        ) {
            $this->refreshConversations();
        }
    }

    /**
     * Update theme when it changes
     */
    public function updateTheme($theme)
    {
        $this->theme = $theme;
    }

    /**
     * Check if user is on mobile device
     */
    protected function checkMobileDevice()
    {
        $userAgent = request()->header('User-Agent');
        return preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent);
    }

    /**
     * Toggle mobile view mode
     */
    public function toggleMobileView($isShowingChat = null)
    {
        if ($isShowingChat !== null) {
            $this->isMobileView = $isShowingChat;
        } else {
            $this->isMobileView = !$this->isMobileView;
        }

        // Force JS refresh to handle view
        $this->dispatch('mobileViewToggled', $this->isMobileView);
    }

    public function refreshConversations()
    {
        $this->loadConversations();

        // If the currently selected conversation was updated, refresh it
        if ($this->selectedConversation) {
            $updatedConversation = $this->conversations->find($this->selectedConversation->id);
            if ($updatedConversation) {
                $this->selectedConversation = $updatedConversation;
            }
        }

        $this->dispatch('$refresh');
    }

    public function loadConversations()
    {
        try {
            $query = auth()->user()->conversations()
                ->with(['sender', 'receiver', 'latestMessage.attachments']);

            // Add search functionality
            if (!empty($this->searchTerm)) {
                $searchTerm = '%' . $this->searchTerm . '%';
                $userId = auth()->id();

                $query->where(function ($q) use ($searchTerm, $userId) {
                    // Search in users
                    $q->whereHas('sender', function ($query) use ($searchTerm, $userId) {
                        $query->where('id', '!=', $userId)
                            ->where('name', 'like', $searchTerm);
                    });

                    $q->orWhereHas('receiver', function ($query) use ($searchTerm, $userId) {
                        $query->where('id', '!=', $userId)
                            ->where('name', 'like', $searchTerm);
                    });

                    // Search in messages
                    $q->orWhereHas('messages', function ($query) use ($searchTerm) {
                        $query->where('body', 'like', $searchTerm);
                    });
                });
            }

            $this->conversations = $query->latest('last_message_at')->get();
        } catch (\Exception $e) {
            Log::error("Error loading conversations: " . $e->getMessage());
            $this->conversations = collect();
        }
    }

    public function loadConversation($conversationId)
    {
        try {
            Log::info("Loading conversation ID: " . $conversationId);

            // Eager load everything needed to display conversation
            $this->selectedConversation = Conversation::with([
                'sender',
                'receiver',
                'messages' => function ($query) {
                    $query->with(['user', 'attachments'])
                        ->orderBy('created_at', 'asc');
                }
            ])->find($conversationId);

            if ($this->selectedConversation) {
                $this->selectedUserId = $this->selectedConversation->getOtherUser(auth()->id())->id;

                // On mobile, change view to show chat
                if ($this->checkMobileDevice()) {
                    $this->toggleMobileView(true);
                }

                // Dispatch event to chat messages component
                $this->dispatch('conversationSelected', $conversationId);

                // Force refresh
                $this->dispatch('$refresh');
            } else {
                Log::error("Conversation not found: " . $conversationId);
            }
        } catch (\Exception $e) {
            Log::error("Error loading conversation: " . $e->getMessage());
        }
    }

    public function handleNewMessage()
    {
        $this->refreshConversations();
    }

    public function updatedSearchTerm()
    {
        $this->loadConversations();
    }

    public function render()
    {
        // Always make sure conversations are loaded
        if (!$this->conversations) {
            $this->loadConversations();
        }

        return view('livewire.chat.chat-container', [
            'conversations' => $this->conversations,
            'isMobileView' => $this->isMobileView,
            'theme' => $this->theme
        ]);
    }
}
