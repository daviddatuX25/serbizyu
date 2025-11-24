<?php

namespace App\Livewire;

use Livewire\Component;
use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Listings\Services\WorkflowTemplateService;
use App\Domains\Listings\Services\WorkflowBookmarkService;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class PublicWorkflowBrowser extends Component
{
    public string $search = '';
    public Collection $publicWorkflows;
    public array $bookmarkedWorkflowIds = [];

    protected WorkflowTemplateService $workflowTemplateService;
    protected WorkflowBookmarkService $workflowBookmarkService;

    public function mount(WorkflowTemplateService $workflowTemplateService, WorkflowBookmarkService $workflowBookmarkService)
    {
        $this->workflowTemplateService = $workflowTemplateService;
        $this->workflowBookmarkService = $workflowBookmarkService;
        $this->loadWorkflows();
        $this->loadBookmarkedIds();
    }

    public function updatedSearch()
    {
        $this->loadWorkflows();
    }

    public function loadWorkflows()
    {
        $this->publicWorkflows = WorkflowTemplate::where('is_public', true)
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->with('workTemplates')
            ->orderBy('name')
            ->get();
    }

    public function loadBookmarkedIds()
    {
        /** @var User $user */
        $user = Auth::user();
        if ($user) {
            $this->bookmarkedWorkflowIds = $user->bookmarkedWorkflowTemplates->pluck('workflow_template_id')->toArray();
        } else {
            $this->bookmarkedWorkflowIds = [];
        }
    }

    public function isBookmarked(int $workflowTemplateId): bool
    {
        return in_array($workflowTemplateId, $this->bookmarkedWorkflowIds);
    }

    public function bookmark(WorkflowTemplate $workflowTemplate)
    {
        /** @var User $user */
        $user = Auth::user();

        // Prevent bookmarking own public workflows or private ones
        if ($workflowTemplate->creator_id === $user->id || !$workflowTemplate->is_public) {
            session()->flash('error', 'You cannot bookmark this workflow.');
            return;
        }

        $bookmarked = $this->workflowBookmarkService->bookmarkWorkflow($user, $workflowTemplate);
        if ($bookmarked) {
            $this->loadBookmarkedIds(); // Refresh bookmarked IDs
            session()->flash('success', 'Workflow bookmarked successfully!');
        } else {
            session()->flash('info', 'Workflow already bookmarked.');
        }
    }

    public function unbookmark(WorkflowTemplate $workflowTemplate)
    {
        /** @var User $user */
        $user = Auth::user();

        $unbookmarked = $this->workflowBookmarkService->unbookmarkWorkflow($user, $workflowTemplate);
        if ($unbookmarked) {
            $this->loadBookmarkedIds(); // Refresh bookmarked IDs
            session()->flash('success', 'Workflow unbookmarked successfully!');
        } else {
            session()->flash('info', 'Workflow was not bookmarked.');
        }
    }

    public function render()
    {
        return view('livewire.public-workflow-browser', [
            'workflows' => $this->publicWorkflows,
            'bookmarkedIds' => $this->bookmarkedWorkflowIds,
        ]);
    }
}
