<?php

namespace App\Domains\Listings\Models;
use App\Domains\Listings\Models\WorkTemplate;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkCatalog extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'default_config'
    ];

    protected $casts = [
        'default_config' => 'array',
    ];

    public function workTemplates()
    {
        return $this->hasMany(WorkTemplate::class);
    }
}
