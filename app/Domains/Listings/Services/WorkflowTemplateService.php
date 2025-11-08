<?php

namespace App\Domains\Listings\Services;

use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Listings\Models\WorkTemplate;
use App\Domains\Listings\Models\WorkCatalog;
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
        $workflowTemplate = WorkflowTemplate::find($id)->with('work');
        if ($workflowTemplate == null) {
            throw new ResourceNotFoundException('Workflow template does not exist.');
        }
        if ($workflowTemplate->trashed()) {
            throw new ResourceNotFoundException('Workflow template has been deleted.');
        }

        if ($workflowTemplate->work == null) {
            throw new ResourceNotFoundException('Associated work template does not exist.');
        }

        return $workflowTemplate;
    }

    public function createWorkflowTemplate(array $data): WorkflowTemplate
    {
        // check if already exists, exact matches since the id is the identifier
    
        return WorkflowTemplate::create($data);
    }

    public function getWorkflowTemplatesByCreator(int $creatorId): Collection
    {
        return WorkflowTemplate::where('creator_id', $creatorId)
            ->orWhere('is_public', true)
            ->orderBy('name')
            ->get();
    }

   
}

