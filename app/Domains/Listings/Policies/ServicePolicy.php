<?php

namespace App\Domains\Listings\Policies;

use App\Domains\Listings\Models\Service;
use App\Domains\Users\Models\User;

class ServicePolicy
{
    /**
     * Admin can manage all services
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Service $service): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Service $service): bool
    {
        return $user->id === $service->creator_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Service $service): bool
    {
        return $user->id === $service->creator_id;
    }

    /**
     * Determine whether the user can purchase the model.
     */
    public function purchase(User $user, Service $service): bool
    {
        return $user->id !== $service->creator_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Service $service): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Service $service): bool
    {
        return false;
    }

    /**
     * Determine whether the user can flag a service (report for review).
     */
    public function flag(User $user, Service $service): bool
    {
        return $user->id !== $service->creator_id;
    }

    /**
     * Determine whether the user can suspend/hide a service (admin only).
     */
    public function suspend(User $user, Service $service): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore a suspended service (admin only).
     */
    public function restoreFromSuspension(User $user, Service $service): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can approve a service (admin only).
     */
    public function approve(User $user, Service $service): bool
    {
        return $user->hasRole('admin');
    }
}
