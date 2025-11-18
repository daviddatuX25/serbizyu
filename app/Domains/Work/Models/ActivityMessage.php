<?php

namespace App\Domains\Work\Models;

use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Model;

class ActivityMessage extends Model
{
    protected $fillable = [
        'activity_thread_id',
        'user_id',
        'content',
    ];

    public function activityThread()
    {
        return $this->belongsTo(ActivityThread::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachments()
    {
        return $this->hasMany(ActivityAttachment::class);
    }
}
