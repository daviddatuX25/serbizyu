<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domains\Listings\Services\ServiceService;
use App\Domains\Users\Services\UserService;
use App\Domains\Listings\Services\CategoryService;
use App\Domains\Listings\Services\WorkflowTemplateService;
use Illuminate\Auth\Middleware\Authenticate;
use App\Domains\Common\Services\AddressService;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ServiceService::class, function ($app) {
            return new ServiceService(
                $app->make(UserService::class),
                $app->make(CategoryService::class),
                $app->make(WorkflowTemplateService::class),
                $app->make(AddressService::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
         // Set custom unauthenticated redirect globally
    Authenticate::redirectUsing(function ($request) {
        return route('auth.signin');
    });
    }
}
