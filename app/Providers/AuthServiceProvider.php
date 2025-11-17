<?php

namespace App\Providers;

use App\Domains\Listings\Models\Category;
use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Listings\Models\OpenOfferBid; // Added
use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Listings\Policies\CategoryPolicy;
use App\Domains\Listings\Policies\OpenOfferPolicy;
use App\Domains\Listings\Policies\BidPolicy; // Added
use App\Domains\Listings\Policies\ServicePolicy;
use App\Domains\Listings\Policies\WorkflowPolicy;
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
        WorkflowTemplate::class => WorkflowPolicy::class,
        OpenOffer::class => OpenOfferPolicy::class,
        OpenOfferBid::class => BidPolicy::class, // Added
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}