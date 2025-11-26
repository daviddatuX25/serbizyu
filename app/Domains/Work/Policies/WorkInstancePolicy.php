<?php

namespace App\Domains\Work\Policies;

use App\Domains\Users\Models\User;
use App\Domains\Work\Models\WorkInstance;
use Illuminate\Auth\Access\Response;

class WorkInstancePolicy
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
     * Both buyer and seller can view the work instance and its progress
     * - Seller: Can see their own work and fulfill steps
     * - Buyer: Can see the work progress and send messages
     */
    public function view(User $user, WorkInstance $workInstance): bool
    {
        return $user->id === $workInstance->order->buyer_id ||
               $user->id === $workInstance->order->seller_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false; // Created automatically with orders
    }

    /**
     * Determine whether the user can update the model.
     * Only seller can update via step transitions
     */
    public function update(User $user, WorkInstance $workInstance): bool
    {
        return $user->id === $workInstance->order->seller_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WorkInstance $workInstance): bool
    {
        return false; // Work instances should not be deleted
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WorkInstance $workInstance): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WorkInstance $workInstance): bool
    {
        return false;
    }

    /**
     * Determine whether the user can start a step
     */
    public function startStep(User $user, WorkInstance $workInstance): bool
    {
        return $user->id === $workInstance->order->seller_id && $workInstance->hasStarted() === false;
    }

    /**
     * Determine whether the user can complete a step
     */
    public function completeStep(User $user, WorkInstance $workInstance): bool
    {
        return $user->id === $workInstance->order->seller_id && $workInstance->status !== 'completed';
    }

    /**
     * Determine whether the user can add activity messages
     * Both buyer and seller can send and receive messages about work steps
     */
    public function addActivity(User $user, WorkInstance $workInstance): bool
    {
        return $user->id === $workInstance->order->buyer_id ||
               $user->id === $workInstance->order->seller_id;
    }
}
