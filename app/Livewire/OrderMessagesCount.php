<?php

namespace App\Livewire;

use App\Domains\Orders\Models\Order;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class OrderMessagesCount extends Component
{
    public Order $order;
    public int $unreadCount = 0;

    public function mount(Order $order)
    {
        $this->order = $order;
        $this->calculateUnreadCount();
    }

    public function calculateUnreadCount()
    {
        $this->unreadCount = $this->order->messageThread?->messages()
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
        return view('livewire.order-messages-count');
    }
}
