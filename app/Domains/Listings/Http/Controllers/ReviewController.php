<?php

namespace App\Domains\Listings\Http\Controllers;

use App\Domains\Listings\Http\Requests\StoreServiceReviewRequest;
use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Models\ServiceReview;
use App\Domains\Listings\Services\ServiceReviewService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct(
        private ServiceReviewService $reviewService
    ) {}

    /**
     * Get all reviews for a service.
     */
    public function index(Service $service, Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 15);
        $verifiedOnly = $request->query('verified_only', false);

        $reviews = $verifiedOnly
            ? $this->reviewService->getVerifiedReviews($service, $perPage)
            : $this->reviewService->getServiceReviews($service, $perPage);

        return response()->json([
            'success' => true,
            'data' => $reviews->items(),
            'pagination' => [
                'total' => $reviews->total(),
                'count' => $reviews->count(),
                'per_page' => $reviews->perPage(),
                'current_page' => $reviews->currentPage(),
                'total_pages' => $reviews->lastPage(),
            ],
        ]);
    }

    /**
     * Store a newly created review in storage.
     */
    public function store(StoreServiceReviewRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['reviewer_id'] = Auth::id();

        $review = $this->reviewService->createReview($validated);

        return response()->json([
            'success' => true,
            'message' => 'Review created successfully.',
            'data' => $review,
        ], 201);
    }

    /**
     * Display the specified review.
     */
    public function show(ServiceReview $review): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $review->load(['reviewer', 'service', 'order']),
        ]);
    }

    /**
     * Update the specified review in storage.
     */
    public function update(StoreServiceReviewRequest $request, ServiceReview $review): JsonResponse
    {
        $this->authorize('update', $review);

        $validated = $request->validated();
        $updatedReview = $this->reviewService->updateReview($review, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully.',
            'data' => $updatedReview,
        ]);
    }

    /**
     * Remove the specified review from storage.
     */
    public function destroy(ServiceReview $review): JsonResponse
    {
        $this->authorize('delete', $review);

        $this->reviewService->deleteReview($review);

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully.',
        ]);
    }

    /**
     * Get service review statistics.
     */
    public function getServiceStats(Service $service): JsonResponse
    {
        $avgRating = $this->reviewService->getAverageRating($service);
        $reviewCount = $this->reviewService->getReviewCount($service);
        $verifiedCount = $this->reviewService->getVerifiedReviewCount($service);

        return response()->json([
            'success' => true,
            'data' => [
                'average_rating' => round($avgRating, 2),
                'review_count' => $reviewCount,
                'verified_review_count' => $verifiedCount,
                'rating_percentage' => $reviewCount > 0 ? round(($avgRating / 5) * 100, 2) : 0,
            ],
        ]);
    }

    /**
     * Mark review as helpful.
     */
    public function markHelpful(ServiceReview $review): JsonResponse
    {
        $updated = $this->reviewService->incrementHelpful($review);

        return response()->json([
            'success' => true,
            'message' => 'Review marked as helpful.',
            'data' => $updated,
        ]);
    }
}
