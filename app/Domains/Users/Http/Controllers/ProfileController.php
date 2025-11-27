<?php

namespace App\Domains\Users\Http\Controllers;

use App\Domains\Orders\Models\Order;
use App\Domains\Users\Http\Requests\ProfileUpdateRequest;
use App\Domains\Users\Models\User;
use App\Domains\Users\Services\UserReviewService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    protected $reviewService;

    public function __construct(UserReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    /**
     * Display a user's public profile with reviews.
     */
    public function show(User $user, Request $request): View
    {
        // Load reviews received with reviewer information and media
        $reviews = $user->reviewsReceived()
            ->with(['reviewer', 'reviewer.media'])
            ->latest()
            ->paginate(10);

        // Get average rating and stats
        $averageRating = $user->average_rating ?? 0;
        $totalReviews = $user->reviewsReceived()->count();

        // Check if authenticated user has completed orders with this user
        $authUser = Auth::user();
        $hasCompletedOrders = false;
        $canReviewUser = false;
        $hasExistingReview = false;

        if ($authUser && $authUser->id !== $user->id) {
            // Check if they have completed orders together
            $hasCompletedOrders = Order::where(function ($query) use ($authUser, $user) {
                $query->where([
                    ['buyer_id', $authUser->id],
                    ['seller_id', $user->id],
                ])
                    ->orWhere([
                        ['buyer_id', $user->id],
                        ['seller_id', $authUser->id],
                    ]);
            })
                ->where('status', 'completed')
                ->exists();

            // Can review if has completed orders and hasn't reviewed yet
            if ($hasCompletedOrders) {
                $hasExistingReview = $user->reviewsReceived()
                    ->where('reviewer_id', $authUser->id)
                    ->exists();
                $canReviewUser = ! $hasExistingReview;
            }
        }

        return view('profile.show', [
            'user' => $user,
            'reviews' => $reviews,
            'averageRating' => $averageRating,
            'totalReviews' => $totalReviews,
            'canReviewUser' => $canReviewUser,
            'hasExistingReview' => $hasExistingReview,
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $emailChanged = isset($validated['email']) && $validated['email'] !== $user->email;
        $user->fill($validated);
        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
