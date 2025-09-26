<?php

namespace App\Domains\Common\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
     protected $fillable = [
        'address_type',
        'house_no',
        'street',
        'barangay',
        'town',
        'province',
        'country',
        'lat',
        'lng',
    ];

    public function addressable()
    {
        return $this->morphTo();
    }
}
