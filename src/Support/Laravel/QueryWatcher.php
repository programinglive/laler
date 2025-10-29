<?php

declare(strict_types=1);

namespace Laler\Support\Laravel;

use DateTimeInterface;
use Laler\DumpCaptureManager;
use RuntimeException;

if (!interface_exists(\Illuminate\Contracts\Events\Dispatcher::class) || !class_exists(\Illuminate\Database\Events\QueryExecuted::class)) {
    final class QueryWatcher
    {
        public function __construct(mixed $events, DumpCaptureManager $manager)
        {
            throw new RuntimeException('Laler query watching requires illuminate/events and illuminate/database.');
        }

        public function start(): void
        {
        }

        public function stop(): void
        {
        }
    }

    return;
}

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Events\QueryExecuted;

final class QueryWatcher
{
    private bool $listening = false;

    /** @var callable(QueryExecuted): void|null */
    private $listener = null;

    public function __construct(
        private Dispatcher $events,
        private DumpCaptureManager $manager
    ) {
    }

    public function start(): void
    {
        if ($this->listening) {
            return;
        }

        $this->listener = function (QueryExecuted $event): void {
            $this->manager->dump([
                'sql' => $event->sql,
                'bindings' => $this->normalizeBindings($event->bindings),
                'time_ms' => $event->time,
                'connection' => $event->connectionName,
            ]);
        };

        $this->events->listen(QueryExecuted::class, $this->listener);

        $this->listening = true;
    }

    public function stop(): void
    {
        if (! $this->listening || $this->listener === null) {
            return;
        }

        $this->events->forget(QueryExecuted::class, $this->listener);

        $this->listener = null;
        $this->listening = false;
    }

    /**
     * @param array<int, mixed> $bindings
     * @return array<int, mixed>
     */
    private function normalizeBindings(array $bindings): array
    {
        return array_map(static function ($binding) {
            if ($binding instanceof DateTimeInterface) {
                return $binding->format('Y-m-d H:i:s');
            }

            if (is_resource($binding)) {
                return sprintf('resource(%s)', get_resource_type($binding) ?: 'unknown');
            }

            return $binding;
        }, $bindings);
    }
}
