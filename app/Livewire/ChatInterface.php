<?php

namespace App\Livewire;

use App\Domains\Messaging\Models\MessageThread;
use App\Domains\Messaging\Models\Message;
use App\Domains\Messaging\Models\MessageAttachment;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatInterface extends Component
{
    use WithFileUploads;

    public MessageThread $thread;
    public $newMessage = '';
    public $attachments = [];
    public $messages;

    protected $listeners = ['echo:message-thread.{thread.id},MessageSent' => 'mount'];

    public function mount()
    {
        $this->messages = $this->thread->messages()->with('sender', 'attachments')->latest()->get();
        // Mark messages as read when mounting the component
        $this->markMessagesAsRead();
    }

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required|string|max:255',
            'attachments.*' => 'file|max:10240', // Max 10MB per file
        ]);

        $message = DB::transaction(function () {
            $message = $this->thread->messages()->create([
                'sender_id' => Auth::id(),
                'content' => $this->newMessage,
            ]);

            if (!empty($this->attachments)) {
                foreach ($this->attachments as $file) {
                    $path = $file->store('message_attachments', 'public');
                    $message->attachments()->create([
                        'file_path' => $path,
                        'file_type' => $file->getMimeType(),
                    ]);
                }
            }
            return $message;
        });

        // Broadcast MessageSent event
        // event(new MessageSent($message)); // Will implement this later in broadcasting setup

        $this->reset(['newMessage', 'attachments']);
        $this->messages = $this->thread->messages()->with('sender', 'attachments')->latest()->get(); // Refresh messages
    }

    public function markMessagesAsRead()
    {
        $this->thread->messages()
            ->where('read_at', null)
            ->where('sender_id', '!=', Auth::id())
            ->update(['read_at' => now()]);
    }

    public function render()
    {
        return view('livewire.chat-interface', [
            'messages' => $this->messages,
        ]);
    }
}
