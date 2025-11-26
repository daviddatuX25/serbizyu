<?php

namespace App\Providers;

use App\Domains\Listings\Models\Category;
use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Listings\Models\OpenOfferBid; // Added
use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Models\ServiceReview;
use App\Domains\Listings\Policies\CategoryPolicy;
use App\Domains\Listings\Policies\OpenOfferPolicy;
use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Listings\Policies\OpenOfferBidPolicy;
use App\Domains\Listings\Policies\ServicePolicy;
use App\Domains\Listings\Policies\ServiceReviewPolicy;
use App\Domains\Listings\Policies\WorkflowPolicy;
use App\Domains\Users\Models\UserReview;
use App\Domains\Users\Policies\UserReviewPolicy;
use App\Domains\Work\Models\WorkInstance;
use App\Domains\Work\Policies\WorkInstancePolicy;
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
        ServiceReview::class => ServiceReviewPolicy::class,
        UserReview::class => UserReviewPolicy::class,
        WorkflowTemplate::class => WorkflowPolicy::class,
        \App\Domains\Orders\Models\Order::class => \App\Domains\Orders\Policies\OrderPolicy::class,
        WorkInstance::class => WorkInstancePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}