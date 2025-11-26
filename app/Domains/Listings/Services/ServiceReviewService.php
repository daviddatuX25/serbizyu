<?php

namespace App\Domains\Listings\Services;

use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Models\ServiceReview;
use App\DTO\CreateServiceReviewDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;

class ServiceReviewService
{
    /**
     * Create a new service review
     */
    public function createReview(CreateServiceReviewDTO $dto): ServiceReview
    {
        return ServiceReview::create($dto->toArray());
    }

    /**
     * Update an existing review
     */
    public function updateReview(ServiceReview $review, CreateServiceReviewDTO $dto): ServiceReview
    {
        $review->update($dto->toArray());
        return $review->fresh();
    }

    /**
     * Delete a review
     */
    public function deleteReview(ServiceReview $review): bool
    {
        return $review->delete();
    }

    /**
     * Get reviews for a service
     */
    public function getServiceReviews(Service $service, int $perPage = 15): Paginator
    {
        return $service->serviceReviews()
            ->with(['reviewer', 'service'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get verified purchase reviews only
     */
    public function getVerifiedReviews(Service $service, int $perPage = 15): Paginator
    {
        return $service->serviceReviews()
            ->where('is_verified_purchase', true)
            ->with(['reviewer'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get a single review
     */
    public function getReview(int $reviewId): ?ServiceReview
    {
        return ServiceReview::with(['reviewer', 'service', 'order'])->find($reviewId);
    }

    /**
     * Get average rating for a service
     */
    public function getAverageRating(Service $service): float
    {
        return $service->serviceReviews()->avg('rating') ?? 0;
    }

    /**
     * Get review count for a service
     */
    public function getReviewCount(Service $service): int
    {
        return $service->serviceReviews()->count();
    }

    /**
     * Get verified purchase count for a service
     */
    public function getVerifiedReviewCount(Service $service): int
    {
        return $service->serviceReviews()
            ->where('is_verified_purchase', true)
            ->count();
    }

    /**
     * Mark review as verified purchase (after order completion)
     */
    public function markAsVerifiedPurchase(ServiceReview $review): ServiceReview
    {
        $review->update(['is_verified_purchase' => true]);
        return $review->fresh();
    }

    /**
     * Increment helpful count
     */
    public function incrementHelpful(ServiceReview $review): ServiceReview
    {
        $review->increment('helpful_count');
        return $review->fresh();
    }
}
