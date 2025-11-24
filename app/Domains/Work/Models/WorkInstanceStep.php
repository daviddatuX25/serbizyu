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

    public function workInstance()
    {
        return $this->belongsTo(WorkInstance::class);
    }

    public function workTemplate()
    {
        return $this->belongsTo(WorkTemplate::class);
    }
}
