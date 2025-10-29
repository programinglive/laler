<?php

declare(strict_types=1);

namespace Laler\Tests;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Events\QueryExecuted;
use Laler\DumpCaptureManager;
use Laler\LalerServiceProvider;
use Laler\Http\Middleware\InjectBrowserConsoleLogs;
use Laler\Support\BrowserConsoleRecorder;
use Orchestra\Testbench\TestCase;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;

final class LalerHelperTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('app.key', 'base64:'.base64_encode('01234567890123456789012345678901'));
        $this->app['config']->set('app.cipher', 'AES-256-CBC');

        $this->app['config']->set('database.default', 'sqlite');
        $this->app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

    }

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

    public function test_browser_console_dumper_records_messages(): void
    {
        $manager = $this->app->make(DumpCaptureManager::class);
        $recorder = $this->app->make(BrowserConsoleRecorder::class);

        $manager->dump('hello browser console');

        $messages = $recorder->flush();

        self::assertCount(1, $messages);
        self::assertStringContainsString('hello browser console', $messages[0]);
    }

    public function test_query_watcher_forwards_queries_to_manager(): void
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

        laler_show_queries();

        /** @var ConnectionInterface $connection */
        $connection = $this->app->make('db')->connection();

        $event = new QueryExecuted('select * from users where email = ?', ['john@example.com'], 19.9, $connection);

        $this->app['events']->dispatch($event);

        laler_stop_showing_queries();

        self::assertNotEmpty($captured);

        $payload = $captured[0];

        self::assertSame('select * from users where email = ?', $payload['sql']);
        self::assertSame(['john@example.com'], $payload['bindings']);
        self::assertSame(19.9, $payload['time_ms']);
        self::assertSame($event->connectionName, $payload['connection']);
    }

}
