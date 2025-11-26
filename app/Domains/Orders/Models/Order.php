<?php

namespace App\Domains\Orders\Models;

use App\Domains\Users\Models\User;
use App\Domains\Listings\Models\Service;
use App\Domains\Messaging\Models\MessageThread;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Order extends Model
{
    use HasFactory, LogsActivity;

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
        'payment_method',
        'paid_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'payment_status'])
            ->setDescriptionForEvent(fn(string $eventName) => "Order has been {$eventName}");
    }

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

    public function payment()
    {
        return $this->hasOne(\App\Domains\Payments\Models\Payment::class);
    }

    public function disbursement()
    {
        return $this->hasOne(\App\Domains\Payments\Models\Disbursement::class);
    }

    public function refund()
    {
        return $this->hasOne(\App\Domains\Payments\Models\Refund::class);
    }

    public function messageThread()
    {
        return $this->morphOne(MessageThread::class, 'parent');
    }

    protected static function newFactory()
    {
        return \Database\Factories\OrderFactory::new();
    }
}
