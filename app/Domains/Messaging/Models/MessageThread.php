<?php

namespace App\Domains\Messaging\Models;

use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Model;

class MessageThread extends Model
{
    protected $fillable = [
        'creator_id',
        'title',
        'parent_type',
        'parent_id',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function parent()
    {
        return $this->morphTo();
    }
}
