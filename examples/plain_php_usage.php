<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Laler\DumpCaptureManager;
use Symfony\Component\VarDumper\Dumper\CliDumper;

// Method 1: Using the global helper (simplest)
// The laler() function will automatically create a manager if none exists
laler('Hello from plain PHP!');
laler(['key' => 'value', 'number' => 42]);

// Method 2: Using the laler_manager() helper to configure dumpers
$manager = laler_manager();
$manager->register(new CliDumper());

laler('This will now go through the CLI dumper');
laler(['data' => ['nested' => 'structure']]);

// Method 3: Creating your own manager instance
$customManager = new DumpCaptureManager();
$customManager->register(new CliDumper());

$customManager->dump('Direct manager usage');
$customManager->dump((object) ['status' => 'success', 'timestamp' => time()]);

echo "Plain PHP integration complete!\n";
