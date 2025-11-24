<?php

namespace App\Domains\Orders\Models;

use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Listings\Models\OpenOfferBid;
use App\Domains\Listings\Models\Service;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\OrderFactory::new();
    }

    protected $fillable = [
        'buyer_id',
        'seller_id',
        'service_id',
        'open_offer_id',
        'open_offer_bid_id',
        'price',
        'platform_fee',
        'total_amount',
        'status',
        'payment_status',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'payment_status' => PaymentStatus::class,
        'cancelled_at' => 'datetime',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function openOffer(): BelongsTo
    {
        return $this->belongsTo(OpenOffer::class);
    }

    public function bid(): BelongsTo
    {
        return $this->belongsTo(OpenOfferBid::class, 'open_offer_bid_id');
    }
}
