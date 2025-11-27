<?php

namespace App\Domains\Users\Policies;

use App\Domains\Users\Models\User;
use App\Domains\Users\Models\UserVerification;

class UserVerificationPolicy
{
    /**
     * Admins can perform any action on verifications
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any verifications (admin list).
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the verification.
     */
    public function view(User $user, UserVerification $userVerification): bool
    {
        return $user->id === $userVerification->user_id || $user->hasRole('admin') || $user->hasRole('moderator');
    }

    /**
     * Determine whether the user can view media attached to the verification.
     */
    public function viewMedia(User $user, UserVerification $userVerification): bool
    {
        return $this->view($user, $userVerification);
    }

    /**
     * Determine whether the user can create a verification.
     */
    public function create(User $user): bool
    {
        return ! $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the verification.
     */
    public function update(User $user, UserVerification $userVerification): bool
    {
        // Users can only update their own pending verifications
        return $user->id === $userVerification->user_id && $userVerification->status === 'pending';
    }

    /**
     * Determine whether the user can delete the verification.
     */
    public function delete(User $user, UserVerification $userVerification): bool
    {
        return false; // Verifications should not be deleted
    }

    /**
     * Determine whether an admin can approve a verification.
     */
    public function approve(User $user, UserVerification $userVerification): bool
    {
        return $user->hasRole('admin') && $userVerification->status === 'pending';
    }

    /**
     * Determine whether an admin can reject a verification.
     */
    public function reject(User $user, UserVerification $userVerification): bool
    {
        return $user->hasRole('admin') && $userVerification->status === 'pending';
    }

    /**
     * Determine whether an admin can manage verifications.
     */
    public function manage(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view verification documents.
     */
    public function viewDocuments(User $user, UserVerification $userVerification): bool
    {
        return $user->hasRole('admin') || $user->id === $userVerification->user_id;
    }

    /**
     * Determine whether the user can download verification documents (admin only).
     */
    public function downloadDocuments(User $user, UserVerification $userVerification): bool
    {
        return $user->hasRole('admin');
    }
}
