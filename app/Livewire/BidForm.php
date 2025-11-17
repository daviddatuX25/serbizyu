<?php

namespace App\Livewire;

use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Listings\Services\OpenOfferBidService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class BidForm extends Component
{
    public OpenOffer $openOffer;
    public ?float $amount = null;
    public ?string $message = null;
    public ?int $service_id = null;
    public $services;

    protected $rules = [
        'amount' => 'required|numeric|min:0',
        'message' => 'nullable|string|max:1000',
        'service_id' => 'required|exists:services,id',
    ];

    public function mount(OpenOffer $openOffer)
    {
        $this->openOffer = $openOffer;
        $this->services = Auth::user()->services;
    }

    public function save(OpenOfferBidService $openOfferBidService)
    {
        $this->validate();

        try {
            $openOfferBidService->createBid(
                Auth::user(),
                $this->openOffer,
                [
                    'amount' => $this->amount,
                    'message' => $this->message,
                    'service_id' => $this->service_id,
                ]
            );

            $this->reset(['amount', 'message', 'service_id']);
            $this->dispatch('bid-placed'); // Emit event to refresh bid list
            session()->flash('success', 'Your bid has been placed successfully!');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.bid-form');
    }
}
