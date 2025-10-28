<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\ExpenseRepositoryInterface;
use App\Repositories\VehicleExpenseRepository;
use App\Contracts\ExpenseTransformerInterface;
use App\Transformers\ExpenseTransformer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            ExpenseRepositoryInterface::class,
            VehicleExpenseRepository::class
        );

        $this->app->bind(
            ExpenseTransformerInterface::class,
            ExpenseTransformer::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
