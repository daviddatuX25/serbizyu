<?php

namespace App\Livewire;

use App\Domains\Messaging\Models\MessageThread;
use App\Domains\Messaging\Models\Message;
use App\Domains\Users\Models\User;
use Livewire\Component;
use Livewire\Attributes\Validate;

class MessageThread extends Component
{
    public $threadId;
    #[Validate('required|string|max:5000')]
    public $newMessage = '';
    
    public $thread;
    public $messages = [];
    public $otherUser;

    public function mount($threadId)
    {
        $this->threadId = $threadId;
        $this->thread = \App\Domains\Messaging\Models\MessageThread::findOrFail($threadId);
        $this->loadMessages();
        $this->markAsRead();
    }

    public function loadMessages()
    {
        $this->messages = $this->thread->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($msg) => [
                'id' => $msg->id,
                'content' => $msg->content,
                'sender' => $msg->sender->name,
                'sender_id' => $msg->sender_id,
                'created_at' => $msg->created_at->diffForHumans(),
                'is_mine' => $msg->sender_id === auth()->id(),
            ])
            ->toArray();
    }

    public function sendMessage()
    {
        $this->validate();

        Message::create([
            'thread_id' => $this->threadId,
            'sender_id' => auth()->id(),
            'content' => $this->newMessage,
        ]);

        $this->newMessage = '';
        $this->loadMessages();
        $this->dispatch('message-sent');
    }

    public function markAsRead()
    {
        $this->thread->messages()
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function render()
    {
        return view('livewire.message-thread');
    }
}
