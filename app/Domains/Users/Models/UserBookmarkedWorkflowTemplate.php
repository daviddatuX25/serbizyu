<?php

namespace App\Domains\Users\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Domains\Listings\Models\WorkflowTemplate;

class UserBookmarkedWorkflowTemplate extends Pivot
{
    protected $table = 'user_bookmarked_workflow_templates';

    protected $fillable = [
        'user_id',
        'workflow_template_id',
    ];

    public $incrementing = false; // It's a pivot table with composite primary key
    protected $primaryKey = null; // No single primary key

    /**
     * Get the user that owns the bookmark.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the workflow template that was bookmarked.
     */
    public function workflowTemplate()
    {
        return $this->belongsTo(WorkflowTemplate::class);
    }
}
