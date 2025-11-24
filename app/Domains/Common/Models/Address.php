<?php

namespace App\Domains\Common\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_address',
        'address_hash',
        'api_source',
        'api_id',
        'lat',
        'lng',
    ];

    protected static function newFactory()
    {
        return \Database\Factories\AddressFactory::new();
    }
}