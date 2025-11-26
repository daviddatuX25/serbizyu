<?php

namespace App\Domains\Listings\Policies;

use App\Domains\Listings\Models\ServiceReview;
use App\Domains\Users\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServiceReviewPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the review.
     */
    public function view(User $user, ServiceReview $review): bool
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
    public function update(User $user, ServiceReview $review): bool
    {
        return $user->id === $review->reviewer_id;
    }

    /**
     * Determine whether the user can delete the review.
     */
    public function delete(User $user, ServiceReview $review): bool
    {
        return $user->id === $review->reviewer_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the review.
     */
    public function restore(User $user, ServiceReview $review): bool
    {
        return $user->id === $review->reviewer_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the review.
     */
    public function forceDelete(User $user, ServiceReview $review): bool
    {
        return $user->isAdmin();
    }
}
