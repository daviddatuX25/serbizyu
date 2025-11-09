<?php

namespace App\Domains\Common\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'house_no',
        'street',
        'barangay',
        'town',
        'province',
        'country',
        'lat',
        'lng',
    ];

    protected static function newFactory()
    {
        return \Database\Factories\AddressFactory::new();
    }
}