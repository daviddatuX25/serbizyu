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
     * Display a listing of the resource.
     */
    public function index(OpenOffer $openoffer)
    {
        $bids = $openoffer->bids()->with('user')->latest()->paginate(10);
        return view('creator.bids.index', compact('bids', 'openoffer'));
    }

    /**
     * Display the specified resource.
     */
    public function show(OpenOffer $openoffer, OpenOfferBid $bid)
    {
        $this->authorize('view', $bid);
        return view('creator.bids.show', compact('bid'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OpenOffer $openoffer, OpenOfferBid $bid)
    {
        $this->authorize('update', $bid);
        return view('creator.bids.edit', compact('bid', 'openoffer'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOpenOfferBidRequest $request, OpenOffer $openoffer)
    {
        try {
            $bid = $this->openOfferBidService->createBid(
                Auth::user(),
                $openoffer,
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
    public function update(UpdateOpenOfferBidRequest $request, OpenOffer $openoffer, OpenOfferBid $bid)
    {
        $this->authorize('update', $bid);

        try {
            $bid = $this->openOfferBidService->updateBid(
                $bid,
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
    public function destroy(OpenOffer $openoffer, OpenOfferBid $bid)
    {
        $this->authorize('delete', $bid);

        try {
            $this->openOfferBidService->deleteBid($bid);
            return back()->with('success', 'Bid deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Accept the specified bid.
     */
    public function accept(OpenOffer $openoffer, OpenOfferBid $bid)
    {
        $this->authorize('accept', $bid);

        try {
            $this->openOfferBidService->acceptBid($bid);
            return back()->with('success', 'Bid accepted successfully! Offer closed.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reject the specified bid.
     */
    public function reject(OpenOffer $openoffer, OpenOfferBid $bid)
    {
        $this->authorize('reject', $bid);

        try {
            $this->openOfferBidService->rejectBid($bid);
            return back()->with('success', 'Bid rejected.');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }
}
