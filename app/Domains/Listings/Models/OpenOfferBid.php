<?php

namespace App\Domains\Listings\Models;

use App\Domains\Messaging\Models\MessageThread;
use App\Domains\Users\Models\User;
use App\Enums\BidStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpenOfferBid extends Model
{
    use hasFactory, SoftDeletes;

    protected $table = 'open_offer_bids';

    protected $fillable = [
        'open_offer_id',
        'bidder_id',
        'service_id',
        'amount',
        'message',
        'status',
    ];

    /**
     * The relations to always eager-load
     */
    protected $with = ['bidder.media'];

    protected $casts = [
        'status' => BidStatus::class,
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function openOffer()
    {
        return $this->belongsTo(OpenOffer::class);
    }

    public function bidder()
    {
        return $this->belongsTo(User::class, 'bidder_id');
    }

    public function messageThread()
    {
        return $this->morphOne(MessageThread::class, 'parent');
    }

    protected static function newFactory()
    {
        return \Database\Factories\OpenOfferBidFactory::new();
    }
}
