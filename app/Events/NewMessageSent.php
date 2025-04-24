<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
        Log::info('NewMessageSent event created for message ID: ' . $message->id);
    }

    public function broadcastOn()
    {
        Log::info('Broadcasting message to channel: conversation.' . $this->message->conversation_id);
        return new PrivateChannel('conversation.' . $this->message->conversation_id);
    }

    public function broadcastWith()
    {
        // Pastikan relasi dimuat
        if (!$this->message->relationLoaded('user')) {
            $this->message->load(['user']);
        }

        if (!$this->message->relationLoaded('attachments')) {
            $this->message->load(['attachments']);
        }

        // Konversi ke array dan tambahkan format waktu untuk timezone Asia/Jakarta
        $messageArray = $this->message->toArray();
        $messageArray['created_at_formatted'] = \Carbon\Carbon::parse($this->message->created_at)
            ->setTimezone('Asia/Jakarta')
            ->format('H:i');

        Log::info('Broadcasting message data', ['messageId' => $this->message->id]);

        return [
            'message' => $messageArray
        ];
    }
}
