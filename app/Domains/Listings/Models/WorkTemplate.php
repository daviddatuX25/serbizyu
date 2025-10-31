<?php

namespace App\Domains\Listings\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Listings\Models\WorkflowTemplate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domains\Listings\Models\WorkCatalog;

class WorkTemplate extends Model
{
    use HasFactory;
    protected $fillable = [
            'workflow_template_id',
            'work_catalog_id',
            'order_index',
            'custom_label',
            'custom_config',
        ];

    protected $casts = [
        'custom_config' => 'array',
    ];

    public function workflowTemplate()
    {
        return $this->belongsTo(WorkflowTemplate::class);
    }
    public function workCatalog()
    {
        return $this->belongsTo(WorkCatalog::class);
    }
}