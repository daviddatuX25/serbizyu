<?php

namespace App\Livewire;

use App\Domains\Work\Models\WorkInstance;
use App\Domains\Messaging\Models\MessageThread;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class WorkChat extends Component
{
    public WorkInstance $workInstance;
    public ?MessageThread $thread = null;

    public function mount(WorkInstance $workInstance)
    {
        $this->workInstance = $workInstance;
        $this->thread = MessageThread::where('parent_type', WorkInstance::class)
            ->where('parent_id', $workInstance->id)
            ->first();

        // Create a thread if it doesn't exist
        if (!$this->thread) {
            $this->thread = MessageThread::create([
                'parent_type' => WorkInstance::class,
                'parent_id' => $workInstance->id,
                'title' => 'Work Discussion - #' . $workInstance->id,
                'description' => 'Discussion thread for work instance #' . $workInstance->id,
                'creator_id' => Auth::id(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.work-chat', [
            'workInstance' => $this->workInstance,
            'thread' => $this->thread,
        ]);
    }
}
