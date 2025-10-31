<?php

namespace App\Domains\Listings\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Listings\Models\WorkflowTemplate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domains\Listings\Models\WorkCatalog;

class ListingImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'thumbnail_path',
        'alt_text',
        'order_index',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function imageable()
    {
        return $this->morphTo();
    }

    // Optional helper for generating URLs if using storage
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->path);
    }

    public function getThumbnailUrlAttribute()
    {
        return asset('storage/' . $this->thumbnail_path);
    }

    public function listing()
    {
        return $this->morphTo();
    }
}
