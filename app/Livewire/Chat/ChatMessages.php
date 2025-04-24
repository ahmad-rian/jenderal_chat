<?php

namespace App\Livewire\Chat;

use App\Events\MessageRead;
use App\Events\NewMessageSent;
use App\Models\Message;
use App\Models\Conversation;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ChatMessages extends Component
{
    use WithFileUploads;

    public $conversation;
    public $messageText = '';
    public $attachments = [];
    public $messages = [];
    public $loadMoreButton = false;
    public $loadedMessages = [];

    protected $listeners = [
        'refreshMessages' => 'loadMessages',
        'messageReceived' => 'handleNewMessage',
        'echo-private:conversation.*,NewMessageSent' => 'handlePusherMessage',
        'echo-private:conversation.*,MessageRead' => 'handleMessageReadEvent',
    ];

    public function mount($conversation)
    {
        $this->loadedMessages = [];
        $this->conversation = $conversation;
        $this->loadMessages();
        $this->markAsRead();
    }

    public function handlePusherMessage($event)
    {
        if (
            isset($event['message']['conversation_id']) &&
            $event['message']['conversation_id'] == $this->conversation->id
        ) {
            $this->markAsRead();
            $this->messages[] = $event['message'];
            $this->dispatch('messageAdded', ['containerId' => 'message-container']);
        }
    }

    public function handleMessageReadEvent($event)
    {
        if (isset($event['conversation_id']) && $event['conversation_id'] == $this->conversation->id) {
            foreach ($this->messages as &$message) {
                if ($message['sender_id'] == auth()->id()) {
                    $message['is_read'] = true;
                }
            }
            $this->dispatch('$refresh');
        }
    }

    public function handleNewMessage()
    {
        $this->loadMessages();
        $this->dispatch('messageAdded', ['containerId' => 'message-container']);
    }

    public function loadMessages()
    {
        try {
            $messages = $this->conversation->messages()
                ->with(['user', 'attachments'])
                ->orderBy('created_at', 'asc')
                ->get();

            $this->messages = $messages->toArray();

            foreach ($this->messages as &$message) {
                $message['created_at_formatted'] = \Carbon\Carbon::parse($message['created_at'])
                    ->setTimezone('Asia/Jakarta')
                    ->format('H:i');
            }

            $this->markAsRead();
            $this->dispatch('messagesLoaded');
        } catch (\Exception $e) {
            $this->messages = [];
        }
    }

    public function sendMessage()
    {
        $this->validate([
            'messageText' => 'required_without:attachments',
            'attachments.*' => 'sometimes|file|max:10240',
        ]);

        try {
            $message = $this->conversation->messages()->create([
                'sender_id' => auth()->id(),
                'body' => $this->messageText,
                'type' => count($this->attachments) > 0 ? 'file' : 'text',
            ]);

            if (count($this->attachments) > 0) {
                foreach ($this->attachments as $attachment) {
                    $path = $attachment->store('attachments', 'public');
                    $filename = $attachment->getClientOriginalName();
                    $fileType = $attachment->getClientMimeType();
                    $fileSize = $attachment->getSize();

                    $message->attachments()->create([
                        'file_name' => $filename,
                        'file_path' => $path,
                        'file_type' => $fileType,
                        'file_size' => $fileSize
                    ]);
                }
            }

            $this->conversation->update([
                'last_message_at' => now()
            ]);

            $this->reset(['messageText', 'attachments']);

            $loadedMessage = $message->load(['user', 'attachments']);

            broadcast(new NewMessageSent($loadedMessage))->toOthers();

            $messageArray = $loadedMessage->toArray();
            $messageArray['created_at_formatted'] = \Carbon\Carbon::parse($messageArray['created_at'])
                ->setTimezone('Asia/Jakarta')
                ->format('H:i');

            $this->messages[] = $messageArray;

            $this->dispatch('messageAdded', ['containerId' => 'message-container']);
            $this->dispatch('chatListRefresh');
        } catch (\Exception $e) {
        }
    }

    public function markAsRead()
    {
        try {
            $unreadMessages = $this->conversation->messages()
                ->where('sender_id', '!=', auth()->id())
                ->where('is_read', false)
                ->get();

            if ($unreadMessages->count() > 0) {
                foreach ($unreadMessages as $message) {
                    $message->update([
                        'is_read' => true,
                        'read_at' => now()
                    ]);

                    broadcast(new MessageRead($message))->toOthers();
                }
            }
        } catch (\Exception $e) {
        }
    }

    public function removeAttachment($index)
    {
        unset($this->attachments[$index]);
        $this->attachments = array_values($this->attachments);
    }

    public function render()
    {
        return view('livewire.chat.chat-messages');
    }
}
