<?php

namespace App\Livewire;

use App\Domains\Work\Models\ActivityThread as ActivityThreadModel;
use App\Domains\Work\Models\ActivityMessage;
use App\Domains\Work\Models\ActivityAttachment;
use Livewire\Component;
use Livewire\WithFileUploads;

class ActivityThread extends Component
{
    use WithFileUploads;

    public ActivityThreadModel $activityThread;
    public $newMessage = '';
    public $attachments = [];

    public function getMessagesProperty()
    {
        return $this->activityThread->messages()->with('user', 'attachments')->latest()->get();
    }

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required|string|max:255',
            'attachments.*' => 'file|max:10240', // Max 10MB per file
        ]);

        $activityMessage = $this->activityThread->messages()->create([
            'user_id' => auth()->id(),
            'content' => $this->newMessage,
        ]);

        foreach ($this->attachments as $file) {
            $path = $file->store('activity_attachments', 'public');
            ActivityAttachment::create([
                'activity_message_id' => $activityMessage->id,
                'file_path' => $path,
                'file_type' => $file->getMimeType(),
            ]);
        }

        $this->newMessage = '';
        $this->attachments = [];
    }

    public function render()
    {
        return view('livewire.activity-thread')->poll(2000);
    }
}
