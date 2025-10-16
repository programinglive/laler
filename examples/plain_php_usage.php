<?php

declare(strict_types=1);

if (!function_exists('now')) {
    class LalerPlainPhpNow extends \DateTimeImmutable
    {
        public function toISOString(): string
        {
            return $this->format(DATE_ATOM);
        }
    }

    function now(): LalerPlainPhpNow
    {
        return new LalerPlainPhpNow();
    }
}

require_once __DIR__ . '/vendor/autoload.php';

use Laler\Dumpers\TauriDumper;

$manager = laler_manager();
$manager->register(new TauriDumper('http://localhost:3000'));

// Dumps now show in the Tauri app
laler(['data' => ['nested' => 'structure']]);
