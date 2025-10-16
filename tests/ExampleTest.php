<?php

declare(strict_types=1);

namespace Laler\Tests;

use Laler\DumpCaptureManager;
use Laler\LalerServiceProvider;
use Orchestra\Testbench\TestCase;

final class ExampleTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LalerServiceProvider::class,
        ];
    }

    public function test_service_provider_registers_manager(): void
    {
        $manager = $this->app->make(DumpCaptureManager::class);

        self::assertInstanceOf(DumpCaptureManager::class, $manager);
    }
}
