<?php

namespace App\Providers;

use App\Services\StatsService;
use Illuminate\Support\ServiceProvider;

class StatsProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Singleton for the StatsService
        $this->app->singleton(StatsService::class, function () {
            return new StatsService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Optional: Load stats globally if needed
        $statsService = $this->app->make(StatsService::class);
        $statsService->setGlobalStats();
    }
}
