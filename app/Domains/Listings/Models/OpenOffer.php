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
use Laravel\Scout\Searchable;

class OpenOffer extends Model implements MediableInterface
{
    use HasFactory;
    use SoftDeletes;
    use Mediable;
    use Searchable;

    protected $table = 'open_offers';
    
    protected $fillable = ['title', 'description', 'budget', 'pay_first', 'fulfilled' ,'category_id', 'creator_id', 'workflow_template_id', 'address_id'];

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

    // casts
    protected $casts = [
        'fulfilled' => 'boolean',
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
        return $this->belongsTo(User::class);
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
