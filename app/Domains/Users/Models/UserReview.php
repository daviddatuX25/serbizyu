<?php

namespace App\Domains\Users\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserReview extends Model
{
    protected $fillable = [
        'reviewer_id',
        'reviewee_id',
        'rating',
        'title',
        'comment',
        'tags',
        'helpful_count',
    ];

    protected $casts = [
        'rating' => 'integer',
        'helpful_count' => 'integer',
        'tags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who wrote the review
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Get the user being reviewed
     */
    public function reviewee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }
}
