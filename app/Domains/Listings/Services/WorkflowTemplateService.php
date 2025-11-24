<?php

namespace App\Domains\Listings\Services;

use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Users\Models\User;
use App\Exceptions\ResourceNotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkflowTemplateService
{
    public function getWorkflowTemplate(int $id): WorkflowTemplate
    {
        $workflowTemplate = WorkflowTemplate::with('workTemplates')->find($id);
        if ($workflowTemplate == null) {
            throw new ResourceNotFoundException('Workflow template does not exist.');
        }
        return $workflowTemplate;
    }

    public function getWorkflowTemplatesByCreator(int $creatorId, array $filters = []): Collection
    {
        $query = WorkflowTemplate::where('creator_id', $creatorId)
            ->orderBy('name');

        if (isset($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->get();
    }

    /**
     * Get all workflow templates available to a user, including their own and bookmarked ones.
     *
     * @param User $user
     * @return Collection<int, WorkflowTemplate>
     */
    public function getAvailableWorkflowTemplatesForUser(User $user): Collection
    {
        // Get templates created by the user
        $myTemplates = $this->getWorkflowTemplatesByCreator($user->id);

        // Get bookmarked templates for the user
        // Use an instance of WorkflowBookmarkService, or inject it if this service grows
        $workflowBookmarkService = app(WorkflowBookmarkService::class);
        $bookmarkedTemplates = $workflowBookmarkService->getBookmarkedWorkflowsForUser($user);

        // Merge and ensure uniqueness (in case a user bookmarks their own template, though design should prevent this)
        $allTemplates = $myTemplates->merge($bookmarkedTemplates)->unique('id');

        // Eager load workTemplates for all merged templates
        return $allTemplates->load('workTemplates');
    }

    public function createWorkflowTemplate(array $data): WorkflowTemplate
    {
        return WorkflowTemplate::create($data);
    }

    public function updateWorkflowTemplate(WorkflowTemplate $workflowTemplate, array $data): WorkflowTemplate
    {
        $workflowTemplate->update($data);
        return $workflowTemplate;
    }

    public function deleteWorkflowTemplate(WorkflowTemplate $workflowTemplate): void
    {
        DB::transaction(function () use ($workflowTemplate) {
            $workflowTemplate->workTemplates()->delete();
            $workflowTemplate->delete();
        });
    }

    public function duplicateWorkflowTemplate(WorkflowTemplate $workflowTemplate): WorkflowTemplate
    {
        return DB::transaction(function () use ($workflowTemplate) {
            $newWorkflowTemplate = $workflowTemplate->replicate([
                'name' // You might want to adjust the name of the duplicate
            ]);
            $newWorkflowTemplate->name = $workflowTemplate->name . ' (Copy)';
            $newWorkflowTemplate->save();

            foreach ($workflowTemplate->workTemplates as $workTemplate) {
                $newWorkTemplate = $workTemplate->replicate();
                $newWorkflowTemplate->workTemplates()->save($newWorkTemplate);
            }

            return $newWorkflowTemplate;
        });
    }
}
