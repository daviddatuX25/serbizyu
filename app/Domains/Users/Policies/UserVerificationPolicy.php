<?php

namespace App\Domains\Users\Policies;

use App\Domains\Users\Models\User;
use App\Domains\Users\Models\UserVerification;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserVerificationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Domains\Users\Models\User  $user
     * @param  \App\Domains\Users\Models\UserVerification  $userVerification
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, UserVerification $userVerification)
    {
        // Only the user who owns the verification or an admin can view it.
        return $user->id === $userVerification->user_id || $user->hasRole('admin') || $user->hasRole('moderator');
    }

    /**
     * Determine whether the user can view media attached to the model.
     *
     * @param  \App\Domains\Users\Models\User  $user
     * @param  \App\Domains\Users\Models\UserVerification  $userVerification
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewMedia(User $user, UserVerification $userVerification)
    {
        // The logic is the same as viewing the parent model itself.
        return $this->view($user, $userVerification);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Domains\Users\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        // Any authenticated user can create a verification request.
        return true;
    }

    /**
     * Determine whether an admin can approve or reject models.
     *
     * @param  \App\Domains\Users\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function manage(User $user)
    {
        return $user->hasRole('admin');
    }
}