<?php

declare(strict_types=1);

use Illuminate\Container\Container;
use Laler\DumpCaptureManager;

if (!function_exists('laler')) {
    function laler(mixed ...$values): void
    {
        $container = Container::getInstance();

        if ($container === null) {
            throw new \RuntimeException('Laravel container instance not available.');
        }

        $container->make(DumpCaptureManager::class)->dump(...$values);
    }
}
