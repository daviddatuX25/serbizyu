<?php

namespace App\Domains\Listings\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\Common\Models\Address;

class Service extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'services';
    protected $fillable = ['title', 'description', 'price', 'pay_first', 'category_id', 'creator_id', 'workflow_template_id', 'address_id'];
    protected $casts = [
        'pay_first' => 'boolean',
    ];

    // morph many images
    public function images()
    {
        return $this->morphMany(ListingImage::class, 'imageable');
    }
    public function thumbnail()
    {
        $primaryImage = $this->morphOne(ListingImage::class, 'imageable')
            ->where('is_primary', true)
            ->select(['id', 'path', 'imageable_id', 'imageable_type']); // minimal columns
        
        if ($primaryImage->doesntExist()) {
            // Fallback to the first image if no primary is set
            $primaryImage = $this->morphOne(ListingImage::class, 'imageable')
                ->select(['id', 'path', 'imageable_id', 'imageable_type'])
                ->orderBy('order_index', 'asc');
        }
        return $primaryImage;    
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

    protected static function newFactory()
    {
        return \Database\Factories\ServiceFactory::new();
    }
}
