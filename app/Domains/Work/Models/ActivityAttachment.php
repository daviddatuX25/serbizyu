<?php

namespace App\Domains\Work\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityAttachment extends Model
{
    protected $fillable = [
        'activity_message_id',
        'file_path',
        'file_type',
    ];

    public function activityMessage()
    {
        return $this->belongsTo(ActivityMessage::class);
    }
}
