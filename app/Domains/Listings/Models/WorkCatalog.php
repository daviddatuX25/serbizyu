<?php

namespace App\Domains\Listings\Models;

use Database\Factories\WorkCatalogFactory; // Import the factory
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkCatalog extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): WorkCatalogFactory
    {
        return WorkCatalogFactory::new();
    }

    protected $fillable = [
        'name', 'description', 'category_id',
    ];

    protected $casts = [];

    public function workTemplates()
    {
        return $this->hasMany(WorkTemplate::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
