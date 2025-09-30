<?php

namespace App\Domains\Listings\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Users\Models\User;
use App\Domains\Listings\Models\Category;
use App\Domains\Listings\Models\WorkflowTemplate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'services';
    protected $fillable = ['title', 'description', 'price', 'pay_first', 'category_id', 'creator_id', 'workflow_template_id'];

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

    protected static function newFactory()
    {
        return \Database\Factories\ServiceFactory::new();
    }
}
