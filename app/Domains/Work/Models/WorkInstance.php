<?php

namespace App\Domains\Work\Models;

use App\Domains\Orders\Models\Order;
use App\Domains\Listings\Models\WorkflowTemplate;
use Illuminate\Database\Eloquent\Model;

class WorkInstance extends Model
{
    protected $fillable = [
        'order_id',
        'workflow_template_id',
        'current_step_index',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'current_step_index' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function workflowTemplate()
    {
        return $this->belongsTo(WorkflowTemplate::class);
    }

    public function workInstanceSteps()
    {
        return $this->hasMany(WorkInstanceStep::class)->orderBy('step_index', 'asc');
    }

    /**
     * Get the current step being executed
     */
    public function getCurrentStep()
    {
        return $this->workInstanceSteps()->where('step_index', $this->current_step_index)->first();
    }

    /**
     * Get the next step to be executed
     */
    public function getNextStep()
    {
        return $this->workInstanceSteps()->where('step_index', '>', $this->current_step_index)->first();
    }

    /**
     * Get all completed steps
     */
    public function getCompletedSteps()
    {
        return $this->workInstanceSteps()->where('status', 'completed')->get();
    }

    /**
     * Get the progress percentage
     */
    public function getProgressPercentage()
    {
        $total = $this->workInstanceSteps()->count();
        if ($total === 0) {
            return 0;
        }
        $completed = $this->workInstanceSteps()->where('status', 'completed')->count();
        return round(($completed / $total) * 100);
    }

    /**
     * Check if work instance is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed' || $this->workInstanceSteps()->where('status', '!=', 'completed')->doesntExist();
    }

    /**
     * Check if work instance has started
     */
    public function hasStarted(): bool
    {
        return $this->started_at !== null;
    }

    /**
     * Get activity threads for all steps
     */
    public function getActivityThreads()
    {
        return ActivityThread::whereIn('work_instance_step_id', $this->workInstanceSteps()->pluck('id'))->get();
    }
}
