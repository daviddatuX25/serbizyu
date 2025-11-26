<?php

namespace App\Domains\Users\Policies;

use App\Domains\Users\Models\User;
use App\Domains\Users\Models\UserReview;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserReviewPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the review.
     */
    public function view(User $user, UserReview $review): bool
    {
        return true; // Anyone can view reviews
    }

    /**
     * Determine whether the user can create a review.
     */
    public function create(User $user): bool
    {
        return true; // Authenticated users can create reviews
    }

    /**
     * Determine whether the user can update the review.
     */
    public function update(User $user, UserReview $review): bool
    {
        return $user->id === $review->reviewer_id;
    }

    /**
     * Determine whether the user can delete the review.
     */
    public function delete(User $user, UserReview $review): bool
    {
        return $user->id === $review->reviewer_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the review.
     */
    public function restore(User $user, UserReview $review): bool
    {
        return $user->id === $review->reviewer_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the review.
     */
    public function forceDelete(User $user, UserReview $review): bool
    {
        return $user->isAdmin();
    }
}
