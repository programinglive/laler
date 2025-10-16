<?php

declare(strict_types=1);

namespace Laler;

use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\VarDumper\Cloner\VarCloner;
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

    private VarCloner $cloner;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->cloner = new VarCloner();
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

    public function dump(mixed ...$values): void
    {
        foreach ($values as $value) {
            $this->forwardToDumpers($value);
        }
    }

    private function ensureVarDumperHandler(): void
    {
        if ($this->registered) {
            return;
        }

        VarDumper::setHandler(function ($value): void {
            $this->forwardToDumpers($value);
        });

        $this->registered = true;
    }

    private function forwardToDumpers(mixed $value): void
    {
        if ($this->dumpers === []) {
            return;
        }

        $data = $this->cloner->cloneVar($value);

        foreach ($this->dumpers as $dumper) {
            $dumper->dump($data);
        }
    }
}
