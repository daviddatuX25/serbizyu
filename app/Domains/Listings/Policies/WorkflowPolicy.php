<?php

namespace App\Domains\Listings\Policies;

use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Users\Models\User;
use Illuminate\Auth\Access\Response;

class WorkflowPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WorkflowTemplate $workflowTemplate): bool
    {
        return $user->id === $workflowTemplate->creator_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WorkflowTemplate $workflowTemplate): bool
    {
        return $user->id === $workflowTemplate->creator_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WorkflowTemplate $workflowTemplate): bool
    {
        return $user->id === $workflowTemplate->creator_id;
    }

    /**
     * Determine whether the user can duplicate the model.
     */
    public function duplicate(User $user, WorkflowTemplate $workflowTemplate): bool
    {
        return $user->id === $workflowTemplate->creator_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WorkflowTemplate $workflowTemplate): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WorkflowTemplate $workflowTemplate): bool
    {
        return false;
    }
}
