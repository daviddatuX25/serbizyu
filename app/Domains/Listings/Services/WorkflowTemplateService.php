<?php

namespace App\Domains\Listings\Services;

use App\Domains\Listings\Models\WorkflowTemplate;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;

class WorkflowTemplateService
{
    public function getAllWorkflowTemplates(): Collection
    {
        $workflowTemplates = WorkflowTemplate::all();

        if ($workflowTemplates->isEmpty()) {
            throw new ResourceNotFoundException('No workflow templates found.');
        }

        if ($workflowTemplates->every->trashed()) {
            throw new ResourceNotFoundException('Workflow templates have all been deleted.');
        }
        
        return $workflowTemplates;
    }

    public function getWorkflowTemplate($id): WorkflowTemplate
    {
        // get a workflow template
        $workflowTemplate = WorkflowTemplate::find($id);
        if ($workflowTemplate == null) {
            throw new ResourceNotFoundException('Workflow template does not exist.');
        }
        if ($workflowTemplate->trashed()) {
            throw new ResourceNotFoundException('Workflow template has been deleted.');
        }
        return $workflowTemplate;
    }
}

