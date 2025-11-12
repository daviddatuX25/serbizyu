<?php

namespace App\Domains\Listings\Models;

use Database\Factories\WorkCatalogFactory; // Import the factory
use App\Domains\Listings\Models\WorkTemplate;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'name', 'description', 'default_config', 'price', 'duration_minutes'
    ];

    protected $casts = [
        'default_config' => 'array',
    ];

    public function workTemplates()
    {
        return $this->hasMany(WorkTemplate::class);
    }
}
