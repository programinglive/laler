<?php

declare(strict_types=1);

use Laler\DumpCaptureManager;

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
