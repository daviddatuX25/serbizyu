<?php

namespace App\Livewire;

use App\Domains\Messaging\Models\MessageThread;
use App\Domains\Messaging\Models\Message;
use App\Domains\Users\Models\User;
use Livewire\Component;

class DirectMessage extends Component
{
    public $recipientId;
    public $recipient;
    public $thread;
    public $newMessage = '';
    public $messages = [];

    public function mount($userId)
    {
        $this->recipientId = $userId;
        $this->recipient = User::findOrFail($userId);
        $this->getOrCreateThread();
        $this->loadMessages();
    }

    public function getOrCreateThread()
    {
        $ids = collect([auth()->id(), $this->recipientId])->sort()->values();
        $hash = implode('-', $ids->toArray());

        $this->thread = MessageThread::updateOrCreate(
            ['parent_type' => 'direct', 'parent_id' => crc32($hash)],
            [
                'creator_id' => auth()->id(),
                'title' => "DM: " . auth()->user()->name . " ↔ " . $this->recipient->name,
            ]
        );
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
                'sender_name' => $msg->sender->name,
                'sender_id' => $msg->sender_id,
                'avatar' => $msg->sender->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($msg->sender->name),
                'created_at' => $msg->created_at->format('H:i'),
                'is_mine' => $msg->sender_id === auth()->id(),
            ])
            ->toArray();

        // Mark as read
        $this->thread->messages()
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function sendMessage()
    {
        if (blank($this->newMessage)) {
            return;
        }

        Message::create([
            'thread_id' => $this->thread->id,
            'sender_id' => auth()->id(),
            'content' => trim($this->newMessage),
        ]);

        $this->newMessage = '';
        $this->loadMessages();
        $this->js("document.querySelector('[data-messages]').scrollTop = document.querySelector('[data-messages]').scrollHeight");
    }

    public function render()
    {
        return view('livewire.direct-message');
    }
}
