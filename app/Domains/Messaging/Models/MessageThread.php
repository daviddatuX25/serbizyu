<?php

namespace App\Domains\Messaging\Models;

use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MessageThread extends Model
{
    use HasFactory, SoftDeletes;

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
        return $this->hasMany(Message::class, 'message_thread_id');
    }

    public function parent()
    {
        return $this->morphTo();
    }

    protected static function newFactory()
    {
        return \Database\Factories\Domains\Messaging\Models\MessageThreadFactory::new();
    }
}
