<?php

namespace App\Providers;

use App\Domains\Listings\Models\Category;
use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Policies\CategoryPolicy;
use App\Domains\Listings\Policies\ServicePolicy;
use App\Policies\MediaPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Plank\Mediable\Media;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Category::class => CategoryPolicy::class,
        Service::class => ServicePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}