<?php

namespace App\Livewire;

use Livewire\Component;
use App\Domains\Listings\Services\WorkflowTemplateService;
use App\Domains\Listings\Services\WorkflowBookmarkService;
use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class CreatorWorkflowManager extends Component
{
    public Collection $ownedWorkflows;
    public Collection $bookmarkedWorkflows;

    protected WorkflowTemplateService $workflowTemplateService;
    protected WorkflowBookmarkService $workflowBookmarkService;

    public function mount(WorkflowTemplateService $workflowTemplateService, WorkflowBookmarkService $workflowBookmarkService)
    {
        $this->workflowTemplateService = $workflowTemplateService;
        $this->workflowBookmarkService = $workflowBookmarkService;
        $this->loadWorkflows();
    }

    public function loadWorkflows()
    {
        /** @var User $user */
        $user = Auth::user();
        if ($user) {
            $this->ownedWorkflows = $this->workflowTemplateService->getWorkflowTemplatesByCreator($user->id);
            $this->bookmarkedWorkflows = $this->workflowBookmarkService->getBookmarkedWorkflowsForUser($user);
        } else {
            $this->ownedWorkflows = new Collection();
            $this->bookmarkedWorkflows = new Collection();
        }
    }

    public function delete(int $workflowTemplateId)
    {
        $workflowTemplate = WorkflowTemplate::findOrFail($workflowTemplateId);
        
        // Authorize that the user owns this template
        if ($workflowTemplate->creator_id !== Auth::id()) {
            session()->flash('error', 'You are not authorized to delete this workflow.');
            return;
        }

        $this->workflowTemplateService->deleteWorkflowTemplate($workflowTemplate);
        $this->loadWorkflows(); // Refresh lists
        session()->flash('success', 'Workflow deleted successfully.');
    }

    public function unbookmark(int $workflowTemplateId)
    {
        $workflowTemplate = WorkflowTemplate::findOrFail($workflowTemplateId);
        
        /** @var User $user */
        $user = Auth::user();

        $unbookmarked = $this->workflowBookmarkService->unbookmarkWorkflow($user, $workflowTemplate);
        
        if ($unbookmarked) {
            $this->loadWorkflows(); // Refresh lists
            session()->flash('success', 'Workflow unbookmarked successfully.');
        } else {
            session()->flash('error', 'Could not unbookmark workflow.');
        }
    }

    public function render()
    {
        return view('livewire.creator-workflow-manager');
    }
}
