<?php

namespace App\Events;

use App\Domains\Orders\Models\Order;
use App\Domains\Messaging\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;
    public Order $order;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message, Order $order)
    {
        $this->message = $message;
        $this->order = $order;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("order.{$this->order->id}"),
            new PrivateChannel("message-thread.{$this->message->message_thread_id}"),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'message' => $this->message->load('sender', 'attachments'),
            'order_id' => $this->order->id,
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'OrderMessageSent';
    }
}
