<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'is_archived',
        'last_message_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_archived' => 'boolean',
        'last_message_at' => 'datetime',
    ];

    /**
     * Sender relationship
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Receiver relationship
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Messages in this conversation
     */
    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get the latest message
     */
    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    /**
     * Get the other participant of the conversation
     */
    public function getOtherUser($userId)
    {
        if ($this->sender_id == $userId) {
            return $this->receiver;
        }

        return $this->sender;
    }

    /**
     * Count unread messages for a specific user
     */
    public function unreadMessagesCount($userId)
    {
        return $this->messages()
            ->where('is_read', false)
            ->where('sender_id', '!=', $userId)
            ->count();
    }

    /**
     * Find or create a conversation between two users
     */
    public static function findOrCreateConversation($userId1, $userId2)
    {
        $conversation = self::where(function ($query) use ($userId1, $userId2) {
            $query->where('sender_id', $userId1)
                ->where('receiver_id', $userId2);
        })
            ->orWhere(function ($query) use ($userId1, $userId2) {
                $query->where('sender_id', $userId2)
                    ->where('receiver_id', $userId1);
            })
            ->first();

        if (!$conversation) {
            $conversation = self::create([
                'sender_id' => $userId1,
                'receiver_id' => $userId2,
                'last_message_at' => now()
            ]);
        }

        return $conversation;
    }
}
