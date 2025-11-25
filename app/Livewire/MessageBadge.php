<?php

namespace App\Livewire;

use App\Domains\Messaging\Models\Message;
use Livewire\Component;

class MessageBadge extends Component
{
    public $unreadCount = 0;

    public function mount()
    {
        $this->updateUnreadCount();
    }

    public function updateUnreadCount()
    {
        $this->unreadCount = Message::where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->count();
    }

    #[\Livewire\Attributes\On('message-sent')]
    public function onMessageSent()
    {
        $this->updateUnreadCount();
    }

    public function render()
    {
        return view('livewire.message-badge');
    }
}
