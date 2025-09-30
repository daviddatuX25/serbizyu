<?php

namespace App\Domains\Listings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\Users\Models\User;

class WorkflowTemplate extends Model
{
    use hasFactory, SoftDeletes;

    protected $fillable = ['title', 'description, creator_id', 'is_public'];

    public function creator()
    {
        return $this->belongsTo(User::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    protected static function newFactory()
    {
        return \Database\Factories\WorkflowTemplateFactory::new();
    }

}