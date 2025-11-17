<?php

namespace App\Domains\Listings\Http\Controllers;

use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Listings\Models\OpenOfferBid;
use App\Domains\Listings\Services\OpenOfferBidService;
use App\Domains\Listings\Http\Requests\StoreOpenOfferBidRequest;
use App\Domains\Listings\Http\Requests\UpdateOpenOfferBidRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OpenOfferBidController extends Controller
{
    protected $openOfferBidService;

    public function __construct(OpenOfferBidService $openOfferBidService)
    {
        $this->openOfferBidService = $openOfferBidService;
        // No authorizeResource here as actions are specific
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOpenOfferBidRequest $request, OpenOffer $openOffer)
    {
        try {
            $bid = $this->openOfferBidService->createBid(
                Auth::user(),
                $openOffer,
                $request->validated()
            );
            return back()->with('success', 'Bid placed successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOpenOfferBidRequest $request, OpenOfferBid $openOfferBid)
    {
        $this->authorize('update', $openOfferBid);

        try {
            $bid = $this->openOfferBidService->updateBid(
                $openOfferBid,
                $request->validated()
            );
            return back()->with('success', 'Bid updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OpenOfferBid $openOfferBid)
    {
        $this->authorize('delete', $openOfferBid);

        try {
            $this->openOfferBidService->deleteBid($openOfferBid);
            return back()->with('success', 'Bid deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Accept the specified bid.
     */
    public function accept(OpenOfferBid $openOfferBid)
    {
        $this->authorize('accept', $openOfferBid);

        try {
            $this->openOfferBidService->acceptBid($openOfferBid);
            return back()->with('success', 'Bid accepted successfully! Offer closed.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reject the specified bid.
     */
    public function reject(OpenOfferBid $openOfferBid)
    {
        $this->authorize('reject', $openOfferBid);

        try {
            $this->openOfferBidService->rejectBid($openOfferBid);
            return back()->with('success', 'Bid rejected.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
