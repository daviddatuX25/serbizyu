<?php

namespace App\Domains\Listings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\Listings\Models\Service;


class OpenOfferBid extends Model
{
    use hasFactory, SoftDeletes;

    protected $table = 'open_offer_bids';
    protected $fillable = [
        'open_offer_id',
        'bidder_id',
        'service_id',
        'proposed_price',
        'accepted'
    ];
    

    public function service()
    {
        return $this->belongsToOne(Service::class);
    }

    public function openOffer()
    {
        return $this->belongsToOne(OpenOffer::class);
    }

    protected static function newFactory()
    {
        return \Database\Factories\OpenOfferBidFactory::new();
    }
}