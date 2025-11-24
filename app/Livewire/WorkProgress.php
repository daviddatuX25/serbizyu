<?php

namespace App\Livewire;

use App\Domains\Work\Models\WorkInstance;
use App\Domains\Work\Models\WorkInstanceStep;
use Livewire\Component;

class WorkProgress extends Component
{
    public WorkInstance $workInstance;

    public function startStep(WorkInstanceStep $step)
    {
        // Logic to start the step
        $step->status = 'in_progress';
        $step->started_at = now();
        $step->save();

        $this->dispatch('work-step-started', $step->id);
    }

    public function completeStep(WorkInstanceStep $step)
    {
        // Logic to complete the step
        $step->status = 'completed';
        $step->completed_at = now();
        $step->save();

        $this->dispatch('work-step-completed', $step->id);
    }

    public function render()
    {
        return view('livewire.work-progress');
    }
}
