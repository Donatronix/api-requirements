<?php

namespace App\Infrastructure\Laravel\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(\App\Domain\Api\Repositories\Contracts\ProductRepository::class, \App\Domain\Api\Repositories\ProductRepositoryEloquent::class);
        $this->app->bind(\App\Domain\Api\Repositories\Contracts\PriceRepository::class, \App\Domain\Api\Repositories\PriceRepositoryEloquent::class);
        //:end-bindings:
    }

}
