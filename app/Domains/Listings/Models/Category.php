<?php

namespace App\Domains\Listings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\Listings\Models\Service;


class Category extends Model
{
    use hasFactory, SoftDeletes;

    protected $table = 'categories';
    protected $fillable = ['name'];

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    public function openOffers()
    {
        return $this->belongsToMany(OpenOffer::class);
    }

    protected static function newFactory()
    {
        return \Database\Factories\CategoryFactory::new();
    }
}