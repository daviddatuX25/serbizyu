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
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
        return $this->hasMany(ActivityAttachment::class)->orderBy('created_at', 'asc');
    }

    /**
     * Mark message as read
     */
    public function markAsRead()
    {
        if ($this->read_at === null) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Check if message has attachments
     */
    public function hasAttachments(): bool
    {
        return $this->attachments()->exists();
    }

    /**
     * Get attachment count
     */
    public function getAttachmentCount()
    {
        return $this->attachments()->count();
    }
}
