<?php

namespace App\Http\Livewire\Chat;

use Livewire\Component;
use Livewire\WithFileUploads;

class MessageInput extends Component
{
    use WithFileUploads;

    public $messageText = '';
    public $attachments = [];
    public $conversation;

    public function mount($conversation)
    {
        $this->conversation = $conversation;
    }

    public function sendMessage()
    {
        $this->emitUp('sendMessage', [
            'text' => $this->messageText,
            'attachments' => $this->attachments
        ]);

        $this->reset(['messageText', 'attachments']);
    }

    public function removeAttachment($index)
    {
        unset($this->attachments[$index]);
        $this->attachments = array_values($this->attachments);
    }

    public function render()
    {
        return view('livewire.chat.message-input');
    }
}
