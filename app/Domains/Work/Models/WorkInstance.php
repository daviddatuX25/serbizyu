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
        return $this->hasMany(WorkInstanceStep::class);
    }
}
