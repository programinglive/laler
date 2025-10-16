<?php

declare(strict_types=1);

namespace Laler\Tests;

use Laler\DumpCaptureManager;
use Laler\LalerServiceProvider;
use Orchestra\Testbench\TestCase;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;

final class LalerHelperTest extends TestCase
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

    public function test_laler_helper_forwards_values_to_registered_dumpers(): void
    {
        $manager = $this->app->make(DumpCaptureManager::class);

        $captured = [];

        $manager->register(new class($captured) implements DataDumperInterface {
            /**
             * @param array<int, mixed> $captured
             */
            public function __construct(private array &$captured)
            {
            }

            public function dump(Data $data): void
            {
                $this->captured[] = $data->getValue(true);
            }
        });

        laler('first value', ['second' => 'value']);

        self::assertSame([
            'first value',
            ['second' => 'value'],
        ], $captured);
    }
}
