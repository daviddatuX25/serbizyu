<?php

namespace App\Domains\Listings\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Users\Models\User;
use App\Domains\Listings\Models\Category;
use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Common\Models\Address;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpenOffer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'open_offers';
    
    protected $fillable = ['title', 'description', 'budget', 'pay_first', 'fulfilled' ,'category_id', 'creator_id', 'workflow_template_id', 'address_id'];

    // casts
    protected $casts = [
        'fulfilled' => 'boolean',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    public function creator()
    {
        return $this->belongsTo(User::class);
    }
    

    public function workflow()
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

    protected function address()
    {
        return $this->belongsTo(Address::class);
    }
}
