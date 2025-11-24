<?php

namespace App\Domains\Listings\Services;

use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Users\Models\User;
use App\Domains\Users\Models\UserBookmarkedWorkflowTemplate;
use Illuminate\Database\Eloquent\Collection;

class WorkflowBookmarkService
{
    /**
     * Bookmark a workflow template for a user.
     *
     * @param User $user
     * @param WorkflowTemplate $workflowTemplate
     * @return UserBookmarkedWorkflowTemplate|null
     */
    public function bookmarkWorkflow(User $user, WorkflowTemplate $workflowTemplate): ?UserBookmarkedWorkflowTemplate
    {
        if ($this->isBookmarked($user, $workflowTemplate)) {
            return null; // Already bookmarked
        }

        return UserBookmarkedWorkflowTemplate::create([
            'user_id' => $user->id,
            'workflow_template_id' => $workflowTemplate->id,
        ]);
    }

    /**
     * Unbookmark a workflow template for a user.
     *
     * @param User $user
     * @param WorkflowTemplate $workflowTemplate
     * @return bool
     */
    public function unbookmarkWorkflow(User $user, WorkflowTemplate $workflowTemplate): bool
    {
        return UserBookmarkedWorkflowTemplate::where('user_id', $user->id)
                                            ->where('workflow_template_id', $workflowTemplate->id)
                                            ->delete();
    }

    /**
     * Check if a workflow template is bookmarked by a user.
     *
     * @param User $user
     * @param WorkflowTemplate $workflowTemplate
     * @return bool
     */
    public function isBookmarked(User $user, WorkflowTemplate $workflowTemplate): bool
    {
        return UserBookmarkedWorkflowTemplate::where('user_id', $user->id)
                                            ->where('workflow_template_id', $workflowTemplate->id)
                                            ->exists();
    }

    /**
     * Get all bookmarked workflow templates for a user.
     *
     * @param User $user
     * @return Collection<int, WorkflowTemplate>
     */
    public function getBookmarkedWorkflowsForUser(User $user): Collection
    {
        return $user->bookmarkedWorkflows()
                    ->with('workTemplates') // Eager load work templates for display
                    ->get();
    }
}
