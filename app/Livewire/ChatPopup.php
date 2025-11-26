<?php

namespace App\Livewire;

use App\Domains\Messaging\Models\MessageThread;
use Livewire\Component;

class ChatPopup extends Component
{
    public ?MessageThread $thread = null;
    public bool $isOpen = false;
    public bool $isMinimized = false;
    public string $title = 'Messages';
    public string $type = 'order'; // order, work, etc.

    protected $listeners = ['openChat' => 'open'];

    public function open(MessageThread $thread)
    {
        $this->thread = $thread;
        $this->isOpen = true;
        $this->isMinimized = false;
    }

    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;
        $this->isMinimized = false;
    }

    public function toggleMinimize()
    {
        $this->isMinimized = !$this->isMinimized;
    }

    public function close()
    {
        $this->isOpen = false;
        $this->isMinimized = false;
    }

    public function render()
    {
        return view('livewire.chat-popup', [
            'thread' => $this->thread,
            'isOpen' => $this->isOpen,
            'isMinimized' => $this->isMinimized,
            'title' => $this->title,
        ]);
    }
}
