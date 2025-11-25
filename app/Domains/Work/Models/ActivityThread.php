<?php

namespace App\Domains\Work\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityThread extends Model
{
    protected $fillable = [
        'work_instance_step_id',
        'title',
        'description',
    ];

    public function workInstanceStep()
    {
        return $this->belongsTo(WorkInstanceStep::class);
    }

    public function messages()
    {
        return $this->hasMany(ActivityMessage::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get unread messages count for a specific user
     */
    public function getUnreadCount($userId)
    {
        return $this->messages()
            ->where('user_id', '!=', $userId)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Get latest message
     */
    public function getLatestMessage()
    {
        return $this->messages()->latest('created_at')->first();
    }

    /**
     * Get file attachments from all messages
     */
    public function getAttachments()
    {
        return ActivityAttachment::whereIn(
            'activity_message_id',
            $this->messages()->pluck('id')
        )->get();
    }

    /**
     * Get message count
     */
    public function getMessageCount()
    {
        return $this->messages()->count();
    }
}
