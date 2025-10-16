<?php

declare(strict_types=1);

namespace Laler;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class LalerServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function register(): void
    {
        $this->app->singleton(DumpCaptureManager::class, static function (Application $app) {
            return new DumpCaptureManager($app);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Future: publish config, setup dump handlers, etc.
    }
}
