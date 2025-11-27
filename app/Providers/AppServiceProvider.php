<?php

namespace App\Providers;

use App\Domains\Common\Interfaces\AddressProviderInterface;
use App\Domains\Common\Services\AddressService;
use App\Domains\Common\Services\PhilippineAddressProvider;
use App\Domains\Listings\Services\CategoryService;
use App\Domains\Listings\Services\OpenOfferBidService;
use App\Domains\Listings\Services\OpenOfferService;
use App\Domains\Listings\Services\ServiceService;
use App\Domains\Listings\Services\WorkCatalogService;
use App\Domains\Listings\Services\WorkflowTemplateService;
// Listings Domain Services
use App\Domains\Listings\Services\WorkTemplateService;
use App\Domains\Users\Services\UserService;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
// Users Domain Services
use Illuminate\Support\Facades\View;
// Common Domain Services
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AddressProviderInterface::class, PhilippineAddressProvider::class);

        // Register CategoryService
        $this->app->singleton(CategoryService::class);

        // Register UserService
        $this->app->singleton(UserService::class);

        // Register AddressService
        $this->app->singleton(AddressService::class);

        // Register WorkflowTemplateService
        $this->app->singleton(WorkflowTemplateService::class);

        // Register WorkCatalogService with dependencies
        $this->app->bind(WorkCatalogService::class, function ($app) {
            return new WorkCatalogService(
                $app->make(CategoryService::class),
                $app->make(WorkflowTemplateService::class)
            );
        });

        // Register WorkTemplateService
        $this->app->singleton(WorkTemplateService::class);

        // Register ServiceService with all dependencies
        $this->app->bind(ServiceService::class, function ($app) {
            return new ServiceService(
                $app->make(UserService::class),
                $app->make(CategoryService::class),
                $app->make(WorkflowTemplateService::class),
                $app->make(AddressService::class),
            );
        });

        // Register OpenOfferService with dependencies
        $this->app->bind(OpenOfferService::class, function ($app) {
            return new OpenOfferService(
                $app->make(UserService::class),
                $app->make(CategoryService::class),
                $app->make(WorkflowTemplateService::class)
            );
        });

        // Register OpenOfferBidService with dependencies
        $this->app->bind(OpenOfferBidService::class, function ($app) {
            return new OpenOfferBidService(
                $app->make(UserService::class),
                $app->make(OpenOfferService::class),
                $app->make(ServiceService::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set custom unauthenticated redirect globally
        Authenticate::redirectUsing(function ($request) {
            return route('auth.signin');
        });

        // Share authentication data with navbar across all views
        View::composer('layouts.navbar', function ($view) {
            $authProfileData = Auth::user();
            $view->with('authProfileData', $authProfileData);
        });

        // Optional: Share common data with all views
        View::composer('*', function ($view) {
            // You can share global data here if needed
            // For example, site settings, notifications count, etc.
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
