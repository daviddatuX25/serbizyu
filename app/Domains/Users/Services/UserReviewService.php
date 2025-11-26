<?php

namespace App\Domains\Users\Services;

use App\Domains\Users\Models\User;
use App\Domains\Users\Models\UserReview;
use App\DTO\CreateUserReviewDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;

class UserReviewService
{
    /**
     * Create a new user review
     */
    public function createReview(CreateUserReviewDTO $dto): UserReview
    {
        return UserReview::create($dto->toArray());
    }

    /**
     * Update an existing review
     */
    public function updateReview(UserReview $review, CreateUserReviewDTO $dto): UserReview
    {
        $review->update($dto->toArray());
        return $review->fresh();
    }

    /**
     * Delete a review
     */
    public function deleteReview(UserReview $review): bool
    {
        return $review->delete();
    }

    /**
     * Get reviews for a user (reviews received)
     */
    public function getUserReviews(User $user, int $perPage = 15): Paginator
    {
        return $user->reviewsReceived()
            ->with(['reviewer'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get reviews written by a user
     */
    public function getUserReviewsWritten(User $user, int $perPage = 15): Paginator
    {
        return $user->reviewsGiven()
            ->with(['reviewee'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get a single review
     */
    public function getReview(int $reviewId): ?UserReview
    {
        return UserReview::with(['reviewer', 'reviewee'])->find($reviewId);
    }

    /**
     * Get average rating for a user
     */
    public function getAverageRating(User $user): float
    {
        return $user->reviewsReceived()->avg('rating') ?? 0;
    }

    /**
     * Get review count for a user
     */
    public function getReviewCount(User $user): int
    {
        return $user->reviewsReceived()->count();
    }
}
