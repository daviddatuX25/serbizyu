<?php

namespace App\Livewire;

use App\Domains\Listings\Models\OpenOfferBid;
use App\Domains\Messaging\Models\MessageThread;
use Livewire\Component;

class BidChat extends Component
{
    public OpenOfferBid $bid;
    public ?MessageThread $thread = null;

    public function mount(OpenOfferBid $bid)
    {
        $this->bid = $bid;
        $this->thread = MessageThread::where('parent_type', OpenOfferBid::class)
            ->where('parent_id', $bid->id)
            ->first();
    }

    public function render()
    {
        return view('livewire.bid-chat', [
            'bid' => $this->bid,
            'thread' => $this->thread,
        ]);
    }
}
