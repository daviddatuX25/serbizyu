<?php

namespace App\Livewire;

use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Listings\Models\WorkTemplate;
use App\Domains\Listings\Models\WorkCatalog;
use App\Domains\Listings\Services\WorkCatalogService;
use App\Domains\Listings\Services\WorkTemplateService;
use App\Domains\Listings\Services\WorkflowTemplateService;
use DebugBar\DebugBar;
use Livewire\Component;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class WorkflowBuilder extends Component 
{
    use AuthorizesRequests;

    public WorkflowTemplate $workflowTemplate;
    public Collection $categories;
    
    /**
     * Define the context: 'page' (default) or 'modal'.
     * This will determine the save behavior.
     */
    public $context = 'page';
    // Use public property without type hint for Livewire compatibility
    
    // Memory-based collections (not saved to DB yet)
    public $workTemplates = [];
    public $pendingDeletes = []; // Track steps to delete on save
    
    public $showCatalogModal = false;
    public $workCatalogs = [];
    public $showEditStepModal = false;
    public $editingStep = null;

    public $name = '';
    public $description = '';
    public $is_public = false;

    // Track if changes have been saved
    public $hasSavedChanges = false;
    private $tempIdCounter = -1; // Negative IDs for new items

    public function mount(WorkflowTemplate $workflowTemplate, Collection $categories)
    {
        $this->workflowTemplate = $workflowTemplate;
        $this->categories = $categories;
        
        // Load existing steps into memory
        $this->workTemplates = $workflowTemplate->workTemplates()
            ->orderBy('order')
            ->get()
            ->map(function ($step) {
                // Convert to array for easier manipulation
                return [
                    'id' => $step->id,
                    'workflow_template_id' => $step->workflow_template_id,
                    'name' => $step->name,
                    'description' => $step->description,
                    'order' => $step->order,
                    'work_catalog_id' => $step->work_catalog_id,
                    'is_persisted' => true, // Track if it exists in DB
                ];
            })
            ->toArray();

        $this->name = $workflowTemplate->name ?? '';
        $this->description = $workflowTemplate->description ?? '';
        $this->is_public = $workflowTemplate->is_public ?? false;
        $this->workflowTemplate->category_id = $workflowTemplate->category_id ?? null;
        $this->hasSavedChanges = true;
    }

    /**
     * Check if there are unsaved changes
     */
    public function hasUnsavedChanges(): bool
    {
        if ($this->hasSavedChanges) {
            return false;
        }

        // Reload the model to get fresh data from DB
        $original = WorkflowTemplate::find($this->workflowTemplate->id);

        // Check if workflow template properties have changed
        $workflowChanged = $this->name !== $original->name ||
                          $this->description !== ($original->description ?? '') ||
                          $this->is_public !== $original->is_public;

        // Check if there are pending changes to steps
        $stepsChanged = collect($this->workTemplates)->contains('is_persisted', false) || 
                       !empty($this->pendingDeletes) ||
                       $this->stepsOrderChanged($original);

        return $workflowChanged || $stepsChanged;
    }

    /**
     * Check if step order has changed
     */
    private function stepsOrderChanged($original): bool
    {
        $originalSteps = $original->workTemplates()
            ->orderBy('order')
            ->pluck('order', 'id')
            ->toArray();

        foreach ($this->workTemplates as $step) {
            if ($step['is_persisted'] && isset($originalSteps[$step['id']])) {
                if ($originalSteps[$step['id']] !== $step['order']) {
                    return true;
                }
            }
        }

        return false;
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

    public function addStepFromCatalog($catalogId, WorkCatalogService $workCatalogService)
    {
        $catalogItem = $workCatalogService->getWorkCatalog($catalogId);
        
        // Add to memory, not DB
        $lastOrder = collect($this->workTemplates)->max('order') ?? -1;
        
        $this->workTemplates[] = [
            'id' => $this->tempIdCounter--,
            'workflow_template_id' => $this->workflowTemplate->id,
            'name' => $catalogItem->name,
            'description' => $catalogItem->description,
            'order' => $lastOrder + 1,
            'work_catalog_id' => $catalogItem->id,
            'is_persisted' => false,
        ];
        
        $this->hasSavedChanges = false;
        $this->closeCatalog();
    }

    public function save(WorkflowTemplateService $workflowTemplateService, WorkTemplateService $workTemplateService)
    {
        if (!$this->workflowTemplate->exists) {
            // This is a new template, check 'create' permission
            $this->authorize('create', WorkflowTemplate::class);
        } else {
            // This is an existing template, check 'update'
            $this->authorize('update', $this->workflowTemplate);
        }
        
        // Check if the model is NEW before saving
        $isNew = !$this->workflowTemplate->exists;
        
        // Check if the model is new (doesn't exist in the DB yet)
        $isCreating = !$this->workflowTemplate->exists;

        $data = [
            'name' => $this->name,
            'description' => $this->description ?: null,
            'is_public' => $this->is_public,
            'category_id' => $this->workflowTemplate->category_id,
            'creator_id' => auth()->id(),
        ];

        if ($isCreating) {
            \Barryvdh\Debugbar\Facades\Debugbar::info('Creating new workflow template', $data);
            
            // --- THIS IS THE NEW LOGIC YOU'RE MISSING ---
            // We are CREATING. Call the create method on your service.
            // (Assuming you have a createWorkflowTemplate method in your service)
            $this->workflowTemplate = $workflowTemplateService->createWorkflowTemplate($data);
            \Barryvdh\Debugbar\Facades\Debugbar::info('Created workflow template', $this->workflowTemplate);
            
        } else {
            // We are UPDATING. Your old logic was fine for this.
            // (I'll add the findOrFail fix we discussed)
            $this->workflowTemplate = WorkflowTemplate::findOrFail($this->workflowTemplate->id);
            $workflowTemplateService->updateWorkflowTemplate($this->workflowTemplate, $data);
        }

        // Delete pending deletions
        foreach ($this->pendingDeletes as $stepId) {
            $step = WorkTemplate::find($stepId);
            if ($step) {
                $workTemplateService->deleteWorkTemplate($step);
            }
        }
        $this->pendingDeletes = [];

        // Save or update steps
        foreach ($this->workTemplates as $index => $stepData) {
            if (!$stepData['is_persisted']) {
                // Create new step
                $step = $workTemplateService->createWorkTemplate([
                    'workflow_template_id' => $this->workflowTemplate->id,
                    'name' => $stepData['name'],
                    'description' => $stepData['description'],
                    'order' => $stepData['order'],
                    'work_catalog_id' => $stepData['work_catalog_id'] ?? null,
                ]);
                
                // Update collection with real ID
                $this->workTemplates[$index]['id'] = $step->id;
                $this->workTemplates[$index]['is_persisted'] = true;
            } else {
                // Update existing step
                $step = WorkTemplate::find($stepData['id']);
                if ($step) {
                    $workTemplateService->updateWorkTemplate($step, $stepData);
                }
            }
        }

        $this->workflowTemplate->refresh();
        
        // Mark as saved
        $this->hasSavedChanges = true;

        if ($this->context === 'modal') {
            // --- MODAL CONTEXT ---
            // We are in the ServiceForm modal.
            // Dispatch the event with the new ID for the parent to catch.
            $this->dispatch('workflowCreated', workflowId: $this->workflowTemplate->id);
            
            // No redirect. The parent component will close the modal.

        } else {
            // --- PAGE CONTEXT ---
            // We are on the standalone workflow page.
            // Redirect to the index page with a success message.
            // This mimics the behavior of the controller[cite: 30].
            session()->flash('success', 'Workflow saved successfully.');
            return $this->redirect(route('creator.workflows.index'), navigate: true);
        }
    }

    public function addStep()
    {
        $lastOrder = collect($this->workTemplates)->max('order') ?? -1;
        
        // Add to memory only
        $this->workTemplates[] = [
            'id' => $this->tempIdCounter--,
            'workflow_template_id' => $this->workflowTemplate->id,
            'name' => 'New Step',
            'description' => 'Step description',
            'order' => $lastOrder + 1,
            'work_catalog_id' => null,
            'is_persisted' => false,
        ];
        
        $this->hasSavedChanges = false;
    }

    public function deleteStep($stepId)
    {
        $steps = collect($this->workTemplates);
        
        // Find step in memory
        $step = $steps->firstWhere('id', $stepId);

        if ($step) {
            // If persisted, add to pending deletes
            if ($step['is_persisted']) {
                $this->pendingDeletes[] = $stepId;
            }
            
            // Remove from memory
            $this->workTemplates = $steps->filter(function ($s) use ($stepId) {
                return $s['id'] !== $stepId;
            })->values()->toArray();
            
            $this->hasSavedChanges = false;
        }
    }

    public function editStep($stepId)
    {
        $this->editingStep = collect($this->workTemplates)->firstWhere('id', $stepId);
        $this->showEditStepModal = true;
    }

    public function closeEditStepModal()
    {
        $this->showEditStepModal = false;
        $this->editingStep = null;
    }

    public function updateStep()
    {
        // Update in memory only
        $steps = collect($this->workTemplates);
        $stepIndex = $steps->search(function ($step) {
            return $step['id'] === $this->editingStep['id'];
        });

        if ($stepIndex !== false) {
            $this->workTemplates[$stepIndex] = $this->editingStep;
            $this->hasSavedChanges = false;
        }
        
        $this->closeEditStepModal();
    }

    public function updateStepOrder($orderedIds)
    {
        // Update order in memory
        $steps = collect($this->workTemplates);
        
        foreach ($orderedIds as $item) {
            $stepId = $item['value'];
            $newOrder = $item['order'];
            
            $stepIndex = $steps->search(function ($step) use ($stepId) {
                return $step['id'] == $stepId;
            });
            
            if ($stepIndex !== false) {
                $this->workTemplates[$stepIndex]['order'] = $newOrder;
            }
        }
        
        // Re-sort by order
        $this->workTemplates = collect($this->workTemplates)
            ->sortBy('order')
            ->values()
            ->toArray();
            
        $this->hasSavedChanges = false;
    }

    // Discard changes and reload from DB
    public function discardChanges()
    {
        $fresh = WorkflowTemplate::find($this->workflowTemplate->id);
        $this->mount($fresh);
        $this->hasSavedChanges = true; // Prevent navigation warning
        session()->flash('info', 'Changes discarded.');
    }

    public function render()
    {
        return view('livewire.workflow-builder');
    }
}