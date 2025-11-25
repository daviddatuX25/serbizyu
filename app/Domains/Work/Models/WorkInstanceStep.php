<?php

namespace App\Domains\Work\Models;

use App\Domains\Listings\Models\WorkTemplate;
use Illuminate\Database\Eloquent\Model;

class WorkInstanceStep extends Model
{
    protected $fillable = [
        'work_instance_id',
        'work_template_id',
        'step_index',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'step_index' => 'integer',
    ];

    public function workInstance()
    {
        return $this->belongsTo(WorkInstance::class);
    }

    public function workTemplate()
    {
        return $this->belongsTo(WorkTemplate::class);
    }

    public function activityThread()
    {
        return $this->hasOne(ActivityThread::class);
    }

    /**
     * Check if this step is the current step
     */
    public function isCurrent(): bool
    {
        return $this->workInstance->current_step_index === $this->step_index;
    }

    /**
     * Check if this step is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if this step is in progress
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Get duration in minutes
     */
    public function getDurationMinutes()
    {
        return $this->workTemplate?->duration_minutes ?? 0;
    }
}
