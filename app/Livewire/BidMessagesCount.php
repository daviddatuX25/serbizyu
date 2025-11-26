<?php

namespace App\Livewire;

use App\Domains\Listings\Models\OpenOfferBid;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class BidMessagesCount extends Component
{
    public OpenOfferBid $bid;
    public int $unreadCount = 0;

    public function mount(OpenOfferBid $bid)
    {
        $this->bid = $bid;
        $this->calculateUnreadCount();
    }

    public function calculateUnreadCount()
    {
        $this->unreadCount = $this->bid->messageThread?->messages()
            ->where('read_at', null)
            ->where('sender_id', '!=', Auth::id())
            ->count() ?? 0;
    }

    #[\Livewire\Attributes\On('message-sent')]
    public function onMessageSent()
    {
        $this->calculateUnreadCount();
    }

    public function render()
    {
        return view('livewire.bid-messages-count');
    }
}
