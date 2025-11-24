<?php

namespace App\Domains\Orders\Models;

use App\Domains\Users\Models\User;
use App\Domains\Listings\Models\Service;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
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

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function workInstance()
    {
        return $this->hasOne(\App\Domains\Work\Models\WorkInstance::class);
    }
}
