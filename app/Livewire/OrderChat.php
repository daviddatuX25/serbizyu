<?php

namespace App\Livewire;

use App\Domains\Orders\Models\Order;
use App\Domains\Messaging\Models\MessageThread;
use Livewire\Component;

class OrderChat extends Component
{
    public Order $order;
    public ?MessageThread $thread = null;

    public function mount(Order $order)
    {
        $this->order = $order;
        $this->thread = MessageThread::where('parent_type', Order::class)
            ->where('parent_id', $order->id)
            ->first();
    }

    public function render()
    {
        return view('livewire.order-chat', [
            'order' => $this->order,
            'thread' => $this->thread,
        ]);
    }
}
