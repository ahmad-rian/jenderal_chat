<?php

namespace App\Livewire\Chat;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class UserStatus extends Component
{
    public $userId;
    public $isOnline = false;
    public $lastSeen = null;
    public $statusText = '';

    protected $listeners = [
        'echo:user-status,UserOnlineStatusChanged' => 'handleStatusChange',
        'refresh' => '$refresh'
    ];

    public function mount($userId)
    {
        $this->userId = $userId;
        $this->updateStatus();
    }

    public function handleStatusChange($event)
    {
        // Only update if this event is for our user
        if ($event['user_id'] == $this->userId) {
            $this->isOnline = $event['is_online'];
            $this->lastSeen = $event['last_active_at'];
            $this->updateStatusText();
            $this->dispatch('$refresh');
        }
    }

    public function updateStatus()
    {
        $user = User::find($this->userId);

        if (!$user) {
            $this->statusText = 'Offline';
            return;
        }

        // Get user's online status
        $this->isOnline = $user->is_online;
        $this->lastSeen = $user->last_active_at;

        $this->updateStatusText();
    }

    private function updateStatusText()
    {
        // Format the status text based on online status and last seen
        if ($this->isOnline) {
            $this->statusText = 'Online';
            return;
        }

        if ($this->lastSeen) {
            $lastActive = Carbon::parse($this->lastSeen)->setTimezone('Asia/Jakarta');
            $now = now()->setTimezone('Asia/Jakarta');
            $diff = $now->diffInMinutes($lastActive);

            if ($diff < 1) {
                $this->statusText = 'Baru saja';
            } elseif ($diff < 60) {
                $this->statusText = $diff . ' menit yang lalu';
            } elseif ($diff < 1440) {
                $hours = floor($diff / 60);
                $this->statusText = $hours . ' jam yang lalu';
            } else {
                $this->statusText = $lastActive->format('d M, H:i');
            }
        } else {
            $this->statusText = 'Offline';
        }
    }

    public function render()
    {
        return view('livewire.chat.user-status');
    }
}
