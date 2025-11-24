<?php

namespace App\Livewire\Traits;

use App\Domains\Listings\Services\WorkflowTemplateService;
use App\Domains\Listings\Services\WorkflowBookmarkService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait Workflowable
{
    /**
     * The list of available workflow templates.
     * @var array
     */
    public array $workflowTemplates = [];

    /**
     * The ID of the selected workflow template.
     * @var int|null
     */
    public ?int $workflow_template_id = null;

    /**
     * Boot the trait by loading workflow templates and setting the
     * workflow_template_id from an optional model.
     *
     * @param object|null $model An optional model (e.g., Service, OpenOffer) to source the existing workflow_template_id from.
     * @return void
     */
    public function mountWorkflowable(?object $model = null): void
    {
        // Ensure user is authenticated before trying to get their workflows
        if (Auth::check()) {
            $user = Auth::user();
            $this->workflowTemplates = app(WorkflowTemplateService::class)
                ->getAvailableWorkflowTemplatesForUser($user)
                ->map(fn($w) => [
                    'id' => $w->id,
                    'name' => $w->name,
                    'description' => $w->description,
                    'workTemplates' => $w->workTemplates->map(fn($wt) => [
                        'id' => $wt->id,
                        'name' => $wt->name,
                    ])->toArray(),
                ])
                ->toArray();
        } else {
            $this->workflowTemplates = []; // No authenticated user, no workflows
        }

        if ($model && $model->exists) {
            $this->workflow_template_id = $model->workflow_template_id;
        }
    }
}
