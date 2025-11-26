<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CashHandshakeSellerResponded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $handshakeId,
        public int $orderId,
        public string $status,
        public string $sellerResponseAt,
        public ?string $rejectionReason = null,
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel('cash-handshakes.' . $this->handshakeId);
    }

    public function broadcastAs(): string
    {
        return 'seller-responded';
    }

    public function broadcastWith(): array
    {
        return [
            'handshake_id' => $this->handshakeId,
            'order_id' => $this->orderId,
            'status' => $this->status,
            'seller_response_at' => $this->sellerResponseAt,
            'rejection_reason' => $this->rejectionReason,
        ];
    }
}
