<?php

namespace App\Domains\Users\Http\Controllers;

use App\Domains\Users\Http\Requests\StoreUserReviewRequest;
use App\Domains\Users\Models\User;
use App\Domains\Users\Models\UserReview;
use App\Domains\Users\Services\UserReviewService;
use App\DTO\CreateUserReviewDTO;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct(
        private UserReviewService $reviewService
    ) {}

    /**
     * Store a newly created review in storage.
     */
    public function store(StoreUserReviewRequest $request): JsonResponse
    {
        $dto = CreateUserReviewDTO::from(array_merge(
            $request->validated(),
            ['reviewer_id' => Auth::id()]
        ));

        $review = $this->reviewService->createReview($dto);

        return response()->json([
            'success' => true,
            'message' => 'Review created successfully.',
            'data' => $review,
        ], 201);
    }

    /**
     * Display the specified review.
     */
    public function show(UserReview $review): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $review->load(['reviewer', 'reviewee']),
        ]);
    }

    /**
     * Update the specified review in storage.
     */
    public function update(StoreUserReviewRequest $request, UserReview $review): JsonResponse
    {
        $this->authorize('update', $review);

        $dto = CreateUserReviewDTO::from($request->validated());
        $updatedReview = $this->reviewService->updateReview($review, $dto);

        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully.',
            'data' => $updatedReview,
        ]);
    }

    /**
     * Remove the specified review from storage.
     */
    public function destroy(UserReview $review): JsonResponse
    {
        $this->authorize('delete', $review);

        $this->reviewService->deleteReview($review);

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully.',
        ]);
    }

    /**
     * Get all reviews for a user (reviews received).
     */
    public function getUserReviews(User $user): JsonResponse
    {
        $reviews = $this->reviewService->getUserReviews($user);

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
     * Get all reviews written by a user.
     */
    public function getUserReviewsWritten(User $user): JsonResponse
    {
        $reviews = $this->reviewService->getUserReviewsWritten($user);

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
     * Get user review statistics.
     */
    public function getUserStats(User $user): JsonResponse
    {
        $avgRating = $this->reviewService->getAverageRating($user);
        $reviewCount = $this->reviewService->getReviewCount($user);

        return response()->json([
            'success' => true,
            'data' => [
                'average_rating' => round($avgRating, 2),
                'review_count' => $reviewCount,
                'rating_percentage' => $reviewCount > 0 ? round(($avgRating / 5) * 100, 2) : 0,
            ],
        ]);
    }
}
