<?php

namespace App\Domains\Listings\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\Common\Models\Address;
use App\Domains\Listings\Models\ListingReview;
use Plank\Mediable\Mediable;
use Plank\Mediable\MediableInterface;
use Laravel\Scout\Searchable;

use App\Domains\Common\Models\Image;
use App\Enums\PaymentMethod;

class Service extends Model implements MediableInterface
{
    use HasFactory;
    use SoftDeletes;
    use Mediable;
    use Searchable;

    protected $table = 'services';
    protected $fillable = ['title', 'description', 'price', 'pay_first', 'payment_method', 'category_id', 'creator_id', 'workflow_template_id', 'address_id'];
    protected $casts = [
        'pay_first' => 'boolean',
        'payment_method' => PaymentMethod::class,
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['media'];

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

    protected static function newFactory()
    {
        return \Database\Factories\ServiceFactory::new();
    }
}
