<?php

namespace App\Domains\Listings\Policies;

use App\Domains\Listings\Models\Flag;
use App\Domains\Users\Models\User;

class FlagPolicy
{
    /**
     * Admins can perform any action on flags
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any flags (admin list).
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * User can create a flag (report content)
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Only admin can view individual flags
     */
    public function view(User $user, Flag $flag): bool
    {
        return $user->hasRole('admin') || $user->id === $flag->user_id;
    }

    /**
     * Users cannot update flags directly
     */
    public function update(User $user, Flag $flag): bool
    {
        return false;
    }

    /**
     * Flags should not be deleted
     */
    public function delete(User $user, Flag $flag): bool
    {
        return false;
    }

    /**
     * Only admin can approve flags
     */
    public function approve(User $user, Flag $flag): bool
    {
        return $user->hasRole('admin') && $flag->status->value === 'pending';
    }

    /**
     * Only admin can reject flags
     */
    public function reject(User $user, Flag $flag): bool
    {
        return $user->hasRole('admin') && $flag->status->value === 'pending';
    }

    /**
     * Only admin can resolve flags
     */
    public function resolve(User $user, Flag $flag): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Only admin can view flag details and evidence
     */
    public function viewEvidence(User $user, Flag $flag): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Only admin can add notes to flags
     */
    public function addNotes(User $user, Flag $flag): bool
    {
        return $user->hasRole('admin');
    }
}
