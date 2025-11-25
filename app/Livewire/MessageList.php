<?php

namespace App\Livewire;

use App\Domains\Messaging\Models\MessageThread;
use App\Domains\Messaging\Models\Message;
use App\Domains\Users\Models\User;
use Livewire\Component;
use Livewire\Attributes\Validate;

class MessageList extends Component
{
    #[Validate('required|exists:users,id')]
    public $recipientId = null;

    public $threads = [];
    public $selectedThread = null;
    public $messages = [];

    public function mount()
    {
        $this->loadThreads();
    }

    public function loadThreads()
    {
        $this->threads = MessageThread::where('creator_id', auth()->id())
            ->orWhere('creator_id', '!=', auth()->id())
            ->with('messages', 'creator')
            ->latest('updated_at')
            ->limit(20)
            ->get()
            ->toArray();
    }

    public function selectThread($threadId)
    {
        $this->selectedThread = $threadId;
        $thread = MessageThread::findOrFail($threadId);
        $this->messages = $thread->messages()
            ->with('sender')
            ->latest()
            ->get()
            ->reverse()
            ->toArray();
        
        // Mark as read
        $thread->messages()
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function render()
    {
        return view('livewire.message-list');
    }
}
