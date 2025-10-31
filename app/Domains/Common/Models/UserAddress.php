<?php

namespace App\Domains\Common\Models;

use Illuminate\Database\Eloquent\Model;
use \App\Domains\Users\Models\User;
use \App\Domains\Common\Models\Address;

class UserAddress extends Model
{
     protected $fillable = [
        'user_id',
        'address_id',
        'is_primary',
    ];

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
