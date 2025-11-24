<?php

namespace App\Domains\Work\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ActivityAttachment extends Model
{
    protected $fillable = [
        'activity_message_id',
        'file_path',
        'file_type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function activityMessage()
    {
        return $this->belongsTo(ActivityMessage::class);
    }

    /**
     * Get file URL
     */
    public function getUrl()
    {
        return asset('storage/' . $this->file_path);
    }

    /**
     * Get file size in bytes
     */
    public function getSize()
    {
        if (Storage::disk('public')->exists($this->file_path)) {
            return Storage::disk('public')->size($this->file_path);
        }
        return 0;
    }

    /**
     * Get formatted file size
     */
    public function getFormattedSize()
    {
        $size = $this->getSize();
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = max($size, 0);
        $pow = floor(($size ? log($size) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $size /= (1 << (10 * $pow));
        return round($size, 2) . ' ' . $units[$pow];
    }

    /**
     * Get file name from path
     */
    public function getFileName()
    {
        return basename($this->file_path);
    }

    /**
     * Check if file is image
     */
    public function isImage(): bool
    {
        $imageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
        return in_array($this->file_type, $imageTypes);
    }

    /**
     * Check if file is video
     */
    public function isVideo(): bool
    {
        $videoTypes = ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime'];
        return in_array($this->file_type, $videoTypes);
    }

    /**
     * Delete file from storage
     */
    public function deleteFile()
    {
        if (Storage::disk('public')->exists($this->file_path)) {
            Storage::disk('public')->delete($this->file_path);
        }
    }
}
