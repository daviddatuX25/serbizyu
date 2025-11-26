<?php

namespace App\Domains\Listings\Services;

use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Models\ServiceReview;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ServiceReviewService
{
    /**
     * Create a new service review
     */
    public function createReview(array $data): ServiceReview
    {
        $review = ServiceReview::create($data);

        // Update the service's cached average rating
        if ($review->service_id) {
            $service = Service::find($review->service_id);
            if ($service) {
                $service->updateAverageRating();
            }
        }

        return $review;
    }

    /**
     * Update an existing review
     */
    public function updateReview(ServiceReview $review, array $data): ServiceReview
    {
        $review->update($data);
        $review = $review->fresh();

        // Update the service's cached average rating
        if ($review->service_id) {
            $service = Service::find($review->service_id);
            if ($service) {
                $service->updateAverageRating();
            }
        }

        return $review;
    }    /**
     * Delete a review
     */
    public function deleteReview(ServiceReview $review): bool
    {
        $serviceId = $review->service_id;
        $deleted = $review->delete();

        // Update the service's cached average rating
        if ($deleted && $serviceId) {
            $service = Service::find($serviceId);
            if ($service) {
                $service->updateAverageRating();
            }
        }

        return $deleted;
    }

    /**
     * Get reviews for a service
     */
    public function getServiceReviews(Service $service, int $perPage = 15): LengthAwarePaginator
    {
        return $service->serviceReviews()
            ->with(['reviewer', 'service'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get verified purchase reviews only
     */
    public function getVerifiedReviews(Service $service, int $perPage = 15): LengthAwarePaginator
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
        $review = $review->fresh();

        // Update the service's cached average rating
        if ($review->service_id) {
            $service = Service::find($review->service_id);
            if ($service) {
                $service->updateAverageRating();
            }
        }

        return $review;
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
