<?php

namespace App\Domains\Listings\Models;

use App\Domains\Common\Models\Address;
use App\Domains\Users\Models\User;
use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Plank\Mediable\Mediable;
use Plank\Mediable\MediableInterface;

class Service extends Model implements MediableInterface
{
    use HasFactory;
    use Mediable;
    use Searchable;
    use SoftDeletes;

    protected $table = 'services';

    protected $fillable = ['title', 'description', 'price', 'pay_first', 'payment_method', 'category_id', 'creator_id', 'workflow_template_id', 'address_id', 'average_rating', 'status'];

    protected $casts = [
        'pay_first' => 'boolean',
        'payment_method' => PaymentMethod::class,
        'average_rating' => 'float',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['media', 'address', 'category', 'creator.media'];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'average_rating' => $this->average_rating,
            'price' => $this->price,
            'category_id' => $this->category_id,
        ];
    }

    public function getGalleryImagesAttribute()
    {
        // Returns a Collection of Media objects tagged 'gallery'
        return $this->getMedia('gallery');
    }

    public function getThumbnailAttribute()
    {
        // Returns the first media tagged 'gallery'
        return $this->getMedia('gallery')->first();
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class);
    }

    public function workflowTemplate()
    {
        return $this->belongsTo(WorkflowTemplate::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function reviews()
    {
        return $this->morphMany(ListingReview::class, 'listing');
    }

    public function serviceReviews()
    {
        return $this->hasMany(ServiceReview::class, 'service_id');
    }

    public function orders()
    {
        return $this->hasMany(\App\Domains\Orders\Models\Order::class, 'service_id');
    }

    /**
     * Get all flags for this service
     */
    public function flags()
    {
        return $this->morphMany(Flag::class, 'flaggable');
    }

    /**
     * Update and cache the average rating
     */
    public function updateAverageRating(): void
    {
        $avgRating = $this->serviceReviews()->avg('rating') ?? 0;
        $this->update(['average_rating' => round($avgRating, 2)]);
    }

    /**
     * Get average rating from service reviews
     */
    public function getAverageRatingAttribute()
    {
        return $this->serviceReviews()->avg('rating') ?? 0;
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->getFirstMediaUrl('gallery');
    }

    public function getIsActiveAttribute()
    {
        return $this->status === 'active';
    }

    protected static function newFactory()
    {
        return \Database\Factories\ServiceFactory::new();
    }
}
