<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'is_online',
        'last_active_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_active_at' => 'datetime',
        'is_online' => 'boolean',
    ];

    /**
     * Get user initials from name.
     *
     * @return string
     */
    public function initials()
    {
        if (empty($this->name)) {
            return '?';
        }

        $words = explode(' ', $this->name);

        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr(end($words), 0, 1));
        }

        return strtoupper(substr($this->name, 0, 1));
    }

    /**
     * Check if user has Google account linked
     *
     * @return bool
     */
    public function hasGoogleLinked()
    {
        return !is_null($this->google_id);
    }

    /**
     * Get conversations where user is the sender
     */
    // public function sentConversations()
    // {
    //     return $this->hasMany(Conversation::class, 'sender_id');
    // }

    // /**
    //  * Get conversations where user is the receiver
    //  */
    // public function receivedConversations()
    // {
    //     return $this->hasMany(Conversation::class, 'receiver_id');
    // }

    // /**
    //  * Get all conversations for this user (as sender or receiver)
    //  */
    // public function conversations()
    // {
    //     return Conversation::where('sender_id', $this->id)
    //         ->orWhere('receiver_id', $this->id);
    // }

    // /**
    //  * Get all messages sent by this user
    //  */
    // public function messages()
    // {
    //     return $this->hasMany(Message::class, 'sender_id');
    // }

    /**
     * Get conversations with unread messages
     */
    public function unreadConversations()
    {
        return $this->conversations()->whereHas('messages', function ($query) {
            $query->where('is_read', false)
                ->where('sender_id', '!=', $this->id);
        });
    }

    /**
     * Get count of unread messages
     */
    // public function unreadMessagesCount()
    // {
    //     return Message::whereHas('conversation', function ($query) {
    //         $query->where(function ($q) {
    //             $q->where('sender_id', $this->id)
    //                 ->orWhere('receiver_id', $this->id);
    //         });
    //     })
    //         ->where('sender_id', '!=', $this->id)
    //         ->where('is_read', false)
    //         ->count();
    // }

    /**
     * Set user as online
     */
    public function setOnline()
    {
        $this->update([
            'is_online' => true,
            'last_active_at' => now()
        ]);
    }

    /**
     * Set user as offline
     */
    public function setOffline()
    {
        $this->update([
            'is_online' => false,
            'last_active_at' => now()
        ]);
    }

    /**
     * Get online status text
     */
    public function getOnlineStatusText()
    {
        if ($this->is_online) {
            return 'Online';
        } elseif ($this->last_active_at) {
            return 'Terakhir online ' . $this->last_active_at->diffForHumans();
        } else {
            return 'Offline';
        }
    }
}
