<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CountdownProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bindMethod(CountdownJob::class . '@handle', function ($job, $app) {
            return $job->handle();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
