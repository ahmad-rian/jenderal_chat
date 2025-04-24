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

    // Perbaikan: Hapus sintaks echo-private yang rumit dan gunakan nama event sederhana
    protected $listeners = [
        'refreshMessages' => 'loadMessages',
        'messageReceived' => 'handleNewMessage',
        'echo-private:conversation.*,NewMessageSent' => 'handlePusherMessage',
        'echo-private:conversation.*,MessageRead' => 'handleMessageReadEvent',
    ];

    public function mount($conversation)
    {
        $this->conversation = $conversation;
        $this->loadMessages();
        $this->markAsRead();
    }

    // Handler untuk event Pusher NewMessageSent
    public function handlePusherMessage($event)
    {
        Log::info('Pusher message received', ['event' => $event]);

        // Verifikasi bahwa pesan untuk percakapan ini
        if (
            isset($event['message']['conversation_id']) &&
            $event['message']['conversation_id'] == $this->conversation->id
        ) {

            // Segera tandai pesan sebagai dibaca
            $this->markAsRead();

            // Tambahkan pesan baru ke array messages
            $this->messages[] = $event['message'];

            // Dispatch event untuk scroll ke bawah
            $this->dispatch('messageAdded', ['containerId' => 'message-container']);
        }
    }

    // Handler untuk event Pusher MessageRead
    public function handleMessageReadEvent($event)
    {
        Log::info('Message read event received', ['event' => $event]);

        if (isset($event['conversation_id']) && $event['conversation_id'] == $this->conversation->id) {
            // Update status read pada pesan
            foreach ($this->messages as &$message) {
                if ($message['sender_id'] == auth()->id()) {
                    $message['is_read'] = true;
                }
            }

            // Force refresh tampilan
            $this->dispatch('$refresh');
        }
    }

    // Handler untuk pesan baru yang diterima
    public function handleNewMessage()
    {
        Log::info('handleNewMessage called');
        $this->loadMessages();
        $this->dispatch('messageAdded', ['containerId' => 'message-container']);
    }

    public function loadMessages()
    {
        try {
            // Ambil pesan dari database
            $messages = $this->conversation->messages()
                ->with(['user', 'attachments'])
                ->orderBy('created_at', 'asc')
                ->get();

            // Konversi ke array 
            $this->messages = $messages->toArray();

            // Konversi tanggal untuk timezone Asia/Jakarta
            foreach ($this->messages as &$message) {
                $message['created_at_formatted'] = \Carbon\Carbon::parse($message['created_at'])
                    ->setTimezone('Asia/Jakarta')
                    ->format('H:i');
            }

            // Tandai semua pesan sebagai dibaca
            $this->markAsRead();

            // Scroll ke bawah setelah pesan dimuat
            $this->dispatch('messagesLoaded');

            Log::info('Messages loaded successfully: ' . count($this->messages));
        } catch (\Exception $e) {
            Log::error("Error loading messages: " . $e->getMessage());
            $this->messages = [];
        }
    }

    public function sendMessage()
    {
        Log::info('sendMessage method called');

        // Validasi input
        $this->validate([
            'messageText' => 'required_without:attachments',
            'attachments.*' => 'sometimes|file|max:10240', // 10MB
        ]);

        try {
            // Buat pesan
            $message = $this->conversation->messages()->create([
                'sender_id' => auth()->id(),
                'body' => $this->messageText,
                'type' => count($this->attachments) > 0 ? 'file' : 'text',
            ]);

            // Upload lampiran jika ada
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

            // Update timestamp percakapan
            $this->conversation->update([
                'last_message_at' => now()
            ]);

            // Reset form
            $this->reset(['messageText', 'attachments']);

            // Muat pesan yang baru dikirim
            $loadedMessage = $message->load(['user', 'attachments']);

            // Broadcast event ke pengguna lain melalui Pusher
            broadcast(new NewMessageSent($loadedMessage))->toOthers();

            // Segera tambahkan pesan baru ke array messages
            $messageArray = $loadedMessage->toArray();
            $messageArray['created_at_formatted'] = \Carbon\Carbon::parse($messageArray['created_at'])
                ->setTimezone('Asia/Jakarta')
                ->format('H:i');

            $this->messages[] = $messageArray;

            // Dispatch event lokal untuk update UI
            $this->dispatch('messageAdded', ['containerId' => 'message-container']);
            $this->dispatch('chatListRefresh');

            Log:
            info('Message sent successfully');
        } catch (\Exception $e) {
            Log::error("Error sending message: " . $e->getMessage());
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

                    // Broadcast ke pengguna lain bahwa pesan telah dibaca
                    broadcast(new MessageRead($message))->toOthers();
                }
            }
        } catch (\Exception $e) {
            Log::error("Error marking messages as read: " . $e->getMessage());
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
