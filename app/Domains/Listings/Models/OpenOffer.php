<?php

namespace App\Domains\Listings\Models;

use App\Domains\Common\Models\Address;
use App\Domains\Users\Models\User;
use App\Enums\OpenOfferStatus;
use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Plank\Mediable\Media;
use Plank\Mediable\Mediable;
use Plank\Mediable\MediableInterface;

class OpenOffer extends Model implements MediableInterface
{
    use HasFactory;
    use Mediable;
    use Searchable;
    use SoftDeletes;

    protected $table = 'open_offers';

    protected $fillable = [
        'title',
        'description',
        'budget',
        'pay_first',
        'payment_method',
        'category_id',
        'creator_id',
        'workflow_template_id',
        'address_id',
        'deadline',
        'status',
    ];

    // casts
    protected $casts = [
        'deadline' => 'datetime',
        'status' => OpenOfferStatus::class,
        'payment_method' => PaymentMethod::class,
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['media', 'category', 'creator.media'];

    public function thumbnail()
    {
        return $this->belongsTo(Media::class, 'thumbnail_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id'); // Explicitly define foreign key
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'creator_id'); // Alias for creator
    }

    public function workflowTemplate()
    {
        return $this->belongsTo(WorkflowTemplate::class);
    }

    public function bids()
    {
        return $this->hasMany(OpenOfferBid::class);
    }

    /**
     * Get all flags for this open offer
     */
    public function flags()
    {
        return $this->morphMany(Flag::class, 'flaggable');
    }

    protected static function newFactory()
    {
        return \Database\Factories\OpenOfferFactory::new();
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}
