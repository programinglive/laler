<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher as IlluminateDispatcher;
use Illuminate\Container\Container;
use Laler\Dumpers\TauriDumper;
use Laler\DumpCaptureManager;
use Laler\Support\Laravel\QueryWatcher;

// Create a dump manager and expose it globally so the laler() helper reuses it
$dumpManager = new DumpCaptureManager();
$GLOBALS['_laler_manager'] = $dumpManager;
$dumpManager->register(new TauriDumper('http://localhost:3000'));

// Setup a lightweight SQLite connection via Eloquent (for demonstration purposes)
$databasePath = __DIR__ . '/test.sqlite';

if (file_exists($databasePath)) {
    unlink($databasePath);
}

touch($databasePath);

$capsule = new Capsule();
$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => $databasePath,
    'prefix' => '',
]);

$container = new class extends Container {
    public function basePath($path = ''): string
    {
        $base = __DIR__;

        if ($path === '' || $path === null) {
            return $base;
        }

        return $base . DIRECTORY_SEPARATOR . ltrim($path, '\\/');
    }
};

$dispatcher = new IlluminateDispatcher($container);
$capsule->setEventDispatcher($dispatcher);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Create a sample table and seed data
$connection = $capsule->getConnection();
$connection->statement('create table users (id integer primary key autoincrement, name text, email text)');
$connection->table('users')->insert([
    ['name' => 'Alice', 'email' => 'alice@example.com'],
    ['name' => 'Bob', 'email' => 'bob@example.com'],
]);

// Start capturing queries via QueryWatcher
$queryWatcher = new QueryWatcher($dispatcher, $dumpManager);
$queryWatcher->start();

// Run some queries to trigger QueryWatcher
$connection->select('select * from users where email = ?', ['alice@example.com']);
$connection->table('users')->where('name', 'Bob')->update(['email' => 'bobby@example.com']);
$connection->table('users')->where('name', 'Alice')->delete();

// Stop capturing queries (optional, but keeps the listener tidy)
$queryWatcher->stop();

// Simple values
laler('Hello from Laler!');
laler(['user' => ['name' => 'Alice', 'role' => 'admin']]);

// A more complex object
$user = new class {
    public string $name = 'Bob';
    public int $age = 30;
    public array $skills = ['PHP', 'Laravel', 'JavaScript'];
};

laler($user);

// Simulate a database query payload (like our QueryWatcher would send)
laler([
    'sql' => 'select * from users where email = ?',
    'bindings' => ['bob@example.com'],
    'time_ms' => 2.4,
    'connection' => 'mysql',
]);

echo "✅ Sent test dumps to Laler app. Check your desktop app to see the results.\n";
