<?php

namespace App\Domains\Payments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domains\Users\Models\User;
use App\Domains\Orders\Models\Order;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Payment extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'order_id',
        'user_id',
        'amount',
        'platform_fee',
        'total_amount',
        'payment_method',
        'provider_reference',
        'status',
        'paid_at',
        'metadata',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status'])
            ->setDescriptionForEvent(fn(string $eventName) => "Payment has been {$eventName}");
    }

    protected $casts = [
        'paid_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
