<?php

declare(strict_types=1);

namespace Laler;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel as HttpKernelContract;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\ServiceProvider;
use Laler\Dumpers\BrowserConsoleDumper;
use Laler\Http\Middleware\InjectBrowserConsoleLogs;
use Laler\Support\BrowserConsoleRecorder;
use Laler\Support\Laravel\QueryWatcher;

class LalerServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function register(): void
    {
        $this->app->singleton(BrowserConsoleRecorder::class);

        $this->app->singleton(BrowserConsoleDumper::class, static function (Application $app) {
            return new BrowserConsoleDumper($app->make(BrowserConsoleRecorder::class));
        });

        $this->app->singleton(InjectBrowserConsoleLogs::class, static function (Application $app) {
            return new InjectBrowserConsoleLogs($app->make(BrowserConsoleRecorder::class));
        });

        $this->app->singleton(DumpCaptureManager::class, static function (Application $app) {
            $manager = new DumpCaptureManager($app);
            $manager->register($app->make(BrowserConsoleDumper::class));

            return $manager;
        });

        if (class_exists(QueryExecuted::class)) {
            $this->app->singleton(QueryWatcher::class, static function (Application $app) {
                return new QueryWatcher(
                    $app->make(Dispatcher::class),
                    $app->make(DumpCaptureManager::class)
                );
            });
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        /** @var object $kernel */
        $kernel = $this->app->make(HttpKernelContract::class);

        if (method_exists($kernel, 'pushMiddleware')) {
            $kernel->pushMiddleware(InjectBrowserConsoleLogs::class);
        }
    }
}
