<?php

declare(strict_types=1);

namespace Laler;

use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\VarDumper\Dumper\ContextProvider\ContextProviderInterface;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;
use Symfony\Component\VarDumper\VarDumper;

final class DumpCaptureManager
{
    private Application $app;

    /** @var array<int, DataDumperInterface> */
    private array $dumpers = [];

    /** @var array<string, ContextProviderInterface> */
    private array $contextProviders = [];

    private bool $registered = false;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function register(DataDumperInterface $dumper): void
    {
        $this->dumpers[] = $dumper;
        $this->ensureVarDumperHandler();
    }

    public function addContextProvider(string $name, ContextProviderInterface $provider): void
    {
        $this->contextProviders[$name] = $provider;
    }

    private function ensureVarDumperHandler(): void
    {
        if ($this->registered) {
            return;
        }

        VarDumper::setHandler(function ($value) {
            foreach ($this->dumpers as $dumper) {
                $dumper->dump($value);
            }
        });

        $this->registered = true;
    }
}
