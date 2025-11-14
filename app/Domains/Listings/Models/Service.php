<?php

namespace App\Domains\Listings\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\Common\Models\Address;
use Plank\Mediable\Mediable;
use Plank\Mediable\MediableInterface;

use App\Domains\Common\Models\Image;

class Service extends Model implements MediableInterface
{
    use HasFactory;
    use SoftDeletes;
    use Mediable;

    protected $table = 'services';
    protected $fillable = ['title', 'description', 'price', 'pay_first', 'category_id', 'creator_id', 'workflow_template_id', 'address_id'];
    protected $casts = [
        'pay_first' => 'boolean',
    ];


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
