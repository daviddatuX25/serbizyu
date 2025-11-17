<?php

namespace App\Domains\Listings\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Users\Models\User;
use App\Domains\Listings\Models\Category;
use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Common\Models\Address;
use Plank\Mediable\Mediable;
use Plank\Mediable\MediableInterface;
use Plank\Mediable\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\OpenOfferStatus; // Added OpenOfferStatus enum

class OpenOffer extends Model implements MediableInterface
{
    use HasFactory;
    use SoftDeletes;
    use Mediable;

    protected $table = 'open_offers';
    
    protected $fillable = [
        'title',
        'description',
        'budget',
        'pay_first',
        'fulfilled',
        'category_id',
        'creator_id',
        'workflow_template_id',
        'address_id',
        'deadline', // Added deadline
        'status',   // Added status
    ];

    // casts
    protected $casts = [
        'fulfilled' => 'boolean',
        'deadline' => 'datetime', // Added deadline cast
        'status' => OpenOfferStatus::class, // Added status cast
    ];


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


    protected static function newFactory()
    {
        return \Database\Factories\OpenOfferFactory::new();
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}
