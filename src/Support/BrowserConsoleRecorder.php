<?php

declare(strict_types=1);

namespace Laler\Support;

final class BrowserConsoleRecorder
{
    /**
     * @var array<int, string>
     */
    private array $messages = [];

    public function push(string $message): void
    {
        $this->messages[] = $message;
    }

    public function isEmpty(): bool
    {
        return $this->messages === [];
    }

    /**
     * @return array<int, string>
     */
    public function flush(): array
    {
        $messages = $this->messages;
        $this->messages = [];

        return $messages;
    }
}
