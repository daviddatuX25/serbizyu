<?php

namespace App\Livewire;

use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Listings\Models\OpenOfferBid;
use App\Domains\Listings\Services\OpenOfferBidService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class BidList extends Component
{
    public OpenOffer $openOffer;
    public $bids;
    public $services;
    public ?int $editingBidId = null;
    public ?float $editingBidAmount = null;
    public ?string $editingBidMessage = null;
    public ?int $editingBidServiceId = null;

    protected $listeners = ['bid-placed' => 'loadBids'];

    public function mount(OpenOffer $openOffer)
    {
        $this->openOffer = $openOffer;
        $this->loadBids();
        $this->services = Auth::user()->services;
    }

    public function loadBids()
    {
        $this->bids = $this->openOffer->bids()->with('bidder', 'service')->latest()->get();
    }

    public function acceptBid(OpenOfferBidService $openOfferBidService, OpenOfferBid $bid)
    {
        $this->authorize('accept', $bid);

        try {
            $openOfferBidService->acceptBid($bid);
            $this->loadBids();
            session()->flash('success', 'Bid accepted successfully! Offer closed.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function rejectBid(OpenOfferBidService $openOfferBidService, OpenOfferBid $bid)
    {
        $this->authorize('reject', $bid);

        try {
            $openOfferBidService->rejectBid($bid);
            $this->loadBids();
            session()->flash('success', 'Bid rejected.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function editBid(OpenOfferBid $bid)
    {
        $this->editingBidId = $bid->id;
        $this->editingBidAmount = $bid->amount;
        $this->editingBidMessage = $bid->message;
        $this->editingBidServiceId = $bid->service_id;
    }

    public function cancelEditing()
    {
        $this->reset(['editingBidId', 'editingBidAmount', 'editingBidMessage', 'editingBidServiceId']);
    }

    public function updateBid(OpenOfferBidService $openOfferBidService)
    {
        $bid = OpenOfferBid::findOrFail($this->editingBidId);
        $this->authorize('update', $bid);

        $this->validate([
            'editingBidAmount' => 'required|numeric|min:0',
            'editingBidMessage' => 'nullable|string|max:1000',
            'editingBidServiceId' => 'required|exists:services,id',
        ]);

        try {
            $openOfferBidService->updateBid($bid, [
                'amount' => $this->editingBidAmount,
                'message' => $this->editingBidMessage,
                'service_id' => $this->editingBidServiceId,
            ]);

            $this->cancelEditing();
            $this->loadBids();
            session()->flash('success', 'Bid updated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function deleteBid(OpenOfferBidService $openOfferBidService, OpenOfferBid $bid)
    {
        $this->authorize('delete', $bid);

        try {
            $openOfferBidService->deleteBid($bid);
            $this->loadBids();
            session()->flash('success', 'Bid deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.bid-list');
    }
}
