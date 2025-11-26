<?php

namespace App\Events;

use App\Domains\Listings\Models\OpenOfferBid;
use App\Domains\Messaging\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BidMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;
    public OpenOfferBid $bid;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message, OpenOfferBid $bid)
    {
        $this->message = $message;
        $this->bid = $bid;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("bid.{$this->bid->id}"),
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
            'bid_id' => $this->bid->id,
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'BidMessageSent';
    }
}
