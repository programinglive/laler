<?php

declare(strict_types=1);

use Laler\DumpCaptureManager;
use Laler\Support\Laravel\QueryWatcher;

if (!function_exists('laler')) {
    function laler(mixed ...$values): void
    {
        static $manager = null;
        
        if ($manager === null) {
            // Try Laravel container first if available
            if (class_exists('\\Illuminate\\Container\\Container')) {
                $container = \Illuminate\Container\Container::getInstance();
                if ($container !== null && $container->bound(DumpCaptureManager::class)) {
                    $manager = $container->make(DumpCaptureManager::class);
                }
            }
            
            // Fallback to global instance or create new one
            if ($manager === null) {
                $manager = $GLOBALS['_laler_manager'] ?? new DumpCaptureManager();
            }
        }
        
        $manager->dump(...$values);
    }
}

if (!function_exists('laler_manager')) {
    function laler_manager(): DumpCaptureManager
    {
        if (!isset($GLOBALS['_laler_manager'])) {
            $GLOBALS['_laler_manager'] = new DumpCaptureManager();
        }
        
        return $GLOBALS['_laler_manager'];
    }
}

if (!function_exists('laler_query_watcher')) {
    function laler_query_watcher(): QueryWatcher
    {
        if (!class_exists(QueryWatcher::class)) {
            throw new \RuntimeException('laler_query_watcher() requires illuminate/events and illuminate/database.');
        }

        if (class_exists('\Illuminate\Container\Container')) {
            $container = \Illuminate\Container\Container::getInstance();
            if ($container !== null && $container->bound(QueryWatcher::class)) {
                return $container->make(QueryWatcher::class);
            }
        }

        throw new \RuntimeException('QueryWatcher is not bound in the container. Ensure LalerServiceProvider is registered.');
    }
}

if (!function_exists('laler_show_queries')) {
    function laler_show_queries(): void
    {
        laler_query_watcher()->start();
    }
}

if (!function_exists('laler_stop_showing_queries')) {
    function laler_stop_showing_queries(): void
    {
        laler_query_watcher()->stop();
    }
}
