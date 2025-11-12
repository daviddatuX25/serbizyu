<?php

namespace App\Livewire;

use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Listings\Models\WorkCatalog;
use App\Domains\Listings\Services\WorkCatalogService;
use App\Domains\Listings\Services\WorkTemplateService;
use App\Domains\Listings\Services\WorkflowTemplateService;
use Livewire\Component;

class WorkflowBuilder extends Component
{
    public WorkflowTemplate $workflowTemplate;
    public $workTemplates;
    public $showCatalogModal = false;
    public $workCatalogs = [];
    public $showEditStepModal = false;
    public ?array $editingStep = null;

    public string $name = '';
    public string $description = '';
    public bool $is_public = false;

    public function mount(WorkflowTemplate $workflowTemplate)
    {
        $this->workflowTemplate = $workflowTemplate;
        $this->workTemplates = $workflowTemplate->workTemplates()->orderBy('order')->get();
        $this->name = $workflowTemplate->name ?? '';
        $this->description = $workflowTemplate->description ?? '';
        $this->is_public = $workflowTemplate->is_public ?? false;

    }

    public function openCatalog(WorkCatalogService $workCatalogService)
    {
        $this->workCatalogs = $workCatalogService->getAllWorkCatalogs();
        $this->showCatalogModal = true;
    }

    public function closeCatalog()
    {
        $this->showCatalogModal = false;
    }

    public function addStepFromCatalog($catalogId, WorkTemplateService $workTemplateService, WorkCatalogService $workCatalogService)
    {
        $catalogItem = $workCatalogService->getWorkCatalog($catalogId);
        $workTemplateService->createWorkTemplateFromCatalog($this->workflowTemplate, $catalogItem);
        $this->workTemplates = $this->workflowTemplate->workTemplates()->orderBy('order')->get();
        $this->closeCatalog();
    }

    public function save(WorkflowTemplateService $workflowTemplateService)
    {
        $this->workflowTemplate->name = $this->name;
        $this->workflowTemplate->description = $this->description ?? null;
        $this->workflowTemplate->is_public = $this->is_public;
        $workflowTemplateService->updateWorkflowTemplate($this->workflowTemplate, [
            'name' => $this->name,
            'description' => $this->description ?: null,
            'is_public' => $this->is_public,
        ]);
        $this->workflowTemplate->refresh();
        session()->flash('success', 'Workflow saved successfully.');
    }

    public function addStep(WorkTemplateService $workTemplateService)
    {
        $lastStep = $this->workTemplates->last();
        $workTemplateService->createWorkTemplate([
            'workflow_template_id' => $this->workflowTemplate->id,
            'name' => 'New Step',
            'description' => 'Step description',
            'order' => $lastStep ? $lastStep->order + 1 : 0,
        ]);
        $this->workTemplates = $this->workflowTemplate->workTemplates()->orderBy('order')->get();
    }

    public function deleteStep($stepId, WorkTemplateService $workTemplateService)
    {
        $step = $this->workTemplates->find($stepId);
        $workTemplateService->deleteWorkTemplate($step);
        $this->workTemplates = $this->workflowTemplate->workTemplates()->orderBy('order')->get();
    }

    public function editStep($stepId)
    {
        $this->editingStep = $this->workTemplates->find($stepId)->toArray();
        $this->showEditStepModal = true;
    }

    public function closeEditStepModal()
    {
        $this->showEditStepModal = false;
        $this->editingStep = null;
    }

    public function updateStep(WorkTemplateService $workTemplateService)
    {
        $step = $this->workTemplates->find($this->editingStep['id']);
        $workTemplateService->updateWorkTemplate($step, $this->editingStep);
        $this->workTemplates = $this->workflowTemplate->workTemplates()->orderBy('order')->get();
        $this->closeEditStepModal();
    }

    public function updateStepOrder($orderedIds, WorkTemplateService $workTemplateService)
    {
        $steps = collect($orderedIds)->map(function ($item) {
            return ['id' => $item['value'], 'order' => $item['order']];
        })->toArray();

        $workTemplateService->reorderWorkTemplates($this->workflowTemplate, $steps);
        $this->workTemplates = $this->workflowTemplate->workTemplates()->orderBy('order')->get();
    }

    public function render()
    {
        return view('livewire.workflow-builder');
    }
}
