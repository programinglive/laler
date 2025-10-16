<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Laler\DumpCaptureManager;
use Laler\Dumpers\TauriDumper;

// Get the DumpCaptureManager instance (in a real Laravel app, this would be from the container)
$manager = new DumpCaptureManager(app());

// Register the TauriDumper
$manager->register(new TauriDumper('http://localhost:3000'));

// Now all laler() calls will be sent to your Tauri app!
laler('Hello from Laravel!');
laler(['user' => 'John', 'email' => 'john@example.com']);
laler((object) ['status' => 'success', 'data' => [1, 2, 3, 4, 5]]);

// You can also use the laler() helper function
laler('This is a debug message', time());
