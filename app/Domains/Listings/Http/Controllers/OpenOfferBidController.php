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
    }

    public function index(OpenOffer $openoffer)
    {
        $this->authorize('viewAny', [OpenOfferBid::class, $openoffer]);

        if (Auth::user()->id === $openoffer->creator_id) {
            $bids = $openoffer->bids()->with('bidder')->latest()->paginate(10);
        } else {
            $bids = $openoffer->bids()->where('bidder_id', Auth::id())->with('bidder')->latest()->paginate(10);
        }

        return view('creator.openoffers.bids.index', compact('bids', 'openoffer'));
    }

    public function store(StoreOpenOfferBidRequest $request, OpenOffer $openoffer)
    {
        try {
            $this->openOfferBidService->createBid(
                Auth::user(),
                $openoffer,
                $request->validated()
            );
            return back()->with('success', 'Bid placed successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit(OpenOffer $openoffer, OpenOfferBid $bid)
    {
        $this->authorize('update', $bid);
        return view('creator.openoffers.bids.edit', compact('bid', 'openoffer'));
    }

    public function update(UpdateOpenOfferBidRequest $request, OpenOffer $openoffer, OpenOfferBid $bid)
    {
        $this->authorize('update', $bid);

        try {
            $this->openOfferBidService->updateBid(
                $bid,
                $request->validated()
            );
            return redirect()->route('creator.openoffers.bids.index', $openoffer)->with('success', 'Bid updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

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
