<?php

declare(strict_types=1);

namespace Laler\Tests;

use Illuminate\Http\Request;
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

    public function test_middleware_injects_console_logs_into_html_responses(): void
    {
        $request = Request::create('/', 'GET');

        $middleware = $this->app->make(InjectBrowserConsoleLogs::class);

        $response = $middleware->handle($request, static function () {
            laler('Hello World');

            return response('<html><body>Welcome</body></html>', 200, ['Content-Type' => 'text/html']);
        });

        self::assertStringContainsString('console.log', $response->getContent());
        self::assertStringContainsString('Hello World', $response->getContent());
    }
}
