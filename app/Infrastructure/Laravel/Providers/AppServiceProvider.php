<?php

namespace App\Infrastructure\Laravel\Providers;

use App\Domain\Api\Services\Interfaces\ProductServiceInterface;
use App\Domain\Api\Services\ProductService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //$this->app->bind(Interface::class, Implementation::class);
        $this->app->register(RepositoryServiceProvider::class);
        $this->app->bind(ProductServiceInterface::class, ProductService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
