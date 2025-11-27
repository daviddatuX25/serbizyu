<?php

namespace App\Domains\Listings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceReview extends Model
{
    protected $fillable = [
        'reviewer_id',
        'service_id',
        'order_id',
        'rating',
        'title',
        'comment',
        'tags',
        'helpful_count',
        'is_verified_purchase',
    ];

    /**
     * The relations to always eager-load
     */
    protected $with = ['reviewer.media'];

    protected $casts = [
        'rating' => 'integer',
        'helpful_count' => 'integer',
        'tags' => 'array',
        'is_verified_purchase' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who wrote the review
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Users\Models\User::class, 'reviewer_id');
    }

    /**
     * Get the service being reviewed
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * Get the associated order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Orders\Models\Order::class, 'order_id');
    }
}
