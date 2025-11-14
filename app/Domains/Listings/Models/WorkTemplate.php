<?php

namespace App\Domains\Listings\Models;

use Database\Factories\WorkTemplateFactory; // Import the factory
use Illuminate\Database\Eloquent\Model;
use App\Domains\Listings\Models\WorkflowTemplate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domains\Listings\Models\WorkCatalog;

class WorkTemplate extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): WorkTemplateFactory
    {
        return WorkTemplateFactory::new();
    }

    protected $fillable = [
            'workflow_template_id',
            'work_catalog_id',
            'name',
            'description',
            'price',
            'duration_minutes',
            'order',
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