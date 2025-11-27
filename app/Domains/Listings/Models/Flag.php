<?php

namespace App\Domains\Listings\Models;

use App\Domains\Users\Models\User;
use App\Enums\FlagCategory;
use App\Enums\FlagStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Flag extends Model
{
    use HasFactory;

    protected $fillable = [
        'flaggable_id',
        'flaggable_type',
        'user_id',
        'admin_id',
        'category',
        'reason',
        'evidence',
        'status',
        'admin_notes',
        'reviewed_at',
    ];

    protected $casts = [
        'status' => FlagStatus::class,
        'category' => FlagCategory::class,
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the flagged model (Service, OpenOffer, etc.)
     */
    public function flaggable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who reported the flag
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who reviewed the flag
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Scope to get pending flags
     */
    public function scopePending($query)
    {
        return $query->where('status', FlagStatus::Pending->value);
    }

    /**
     * Scope to get flags by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
