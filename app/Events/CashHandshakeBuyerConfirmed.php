<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CashHandshakeBuyerConfirmed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $handshakeId,
        public int $orderId,
        public string $buyerClaimedAt,
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel('cash-handshakes.' . $this->handshakeId);
    }

    public function broadcastAs(): string
    {
        return 'buyer-confirmed';
    }

    public function broadcastWith(): array
    {
        return [
            'handshake_id' => $this->handshakeId,
            'order_id' => $this->orderId,
            'buyer_claimed_at' => $this->buyerClaimedAt,
            'status' => 'buyer_claimed',
        ];
    }
}
