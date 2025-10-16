# Laler

Capture PHP `VarDumper` output and redirect it to any channel you control. Works with or without Laravel.

## Quick Start

1. **Install the library:** `composer require laler/laler`
2. **Install Windows desktop app** (optional): Download and run `installer/lalerapp_0.1.0_x64_en-US.msi` as Administrator
3. **Start debugging:** Use `laler('Your debug data')` in your PHP code
4. **View results** in the desktop app or console

## Requirements
- PHP ^8.0
- Symfony VarDumper ^6.0 || ^7.0
- PSR-11 Container Interface ^1.0 || ^2.0

**Optional (for Laravel integration):**
- Laravel (Illuminate components) ^9.0 || ^12.0

## Installation
```bash
composer require laler/laler
composer install
```

**For Laravel projects:** The package is auto-discovered, so no manual provider registration is required.

**For plain PHP projects:** Include the Composer autoloader and call the `laler()` helper.

## Windows Desktop App

For a visual interface to view your laler() dumps, install the Windows desktop application:

### Installation
1. **Download** the installer: `installer/lalerapp_0.1.0_x64_en-US.msi`
2. **Right-click** → **"Run as Administrator"** (required for system-level HTTP server setup)
3. **Follow** the installation wizard
4. **Launch** the Laler app from Start Menu or Desktop shortcut

### Usage
1. **Start the desktop app** first
2. **Configure your laler() calls** to send dumps to the app (see TauriDumper examples below)
3. **View dumps** in real-time through the desktop interface

> **Note:** The desktop app provides a user-friendly interface for debugging and monitoring your PHP application dumps without cluttering your console or logs.

## Integration Guides

### Plain PHP
- **Boot autoloader** `require 'vendor/autoload.php';`
- **Configure dumpers (optional)** `laler_manager()->register(new CliDumper());`
- **Send values** `laler($value, $moreValues);`

> **Note:** In plain PHP projects, the vendor directory is typically at your project root, so use `__DIR__ . '/vendor/autoload.php'`. In Laravel projects within subdirectories, you might need `__DIR__ . '/../vendor/autoload.php'` depending on your file location.

```php
require __DIR__.'/vendor/autoload.php';

laler('Ready to capture without Laravel!');
```

### Laravel
- **Install** `composer require laler/laler`
- **Register dumpers** (for example, in a service provider)

```php
use Laler\DumpCaptureManager;
use Symfony\Component\VarDumper\Dumper\CliDumper;

public function boot(): void
{
    app(DumpCaptureManager::class)->register(new CliDumper());
}
```

- **Use helper** call `laler()` anywhere to route values through configured dumpers.

## Usage

### Laravel Usage
`DumpCaptureManager` centralises dump collection and forwards each value to your registered dumpers. Retrieve it from the container and register any `DataDumperInterface` implementation:

```php
use Laler\DumpCaptureManager;
use Symfony\Component\VarDumper\Dumper\CliDumper;

$manager = app(DumpCaptureManager::class);
$manager->register(new CliDumper());

laler('Hello from Laler!'); // Routed to the CLI dumper
```

### Plain PHP Usage
For projects without Laravel, use the global helper functions:

```php
use Laler\DumpCaptureManager;
use Symfony\Component\VarDumper\Dumper\CliDumper;

// Method 1: Simple usage (auto-creates manager)
laler('Hello from plain PHP!');
laler(['key' => 'value']);

// Method 2: Configure dumpers using helper
$manager = laler_manager();
$manager->register(new CliDumper());
laler('Now routed through CLI dumper');

// Method 3: Manual manager creation
$manager = new DumpCaptureManager();
$manager->register(new CliDumper());
$manager->dump('Direct usage');
```

Add extra context to every dump by registering `ContextProviderInterface` implementations:

```php
use Symfony\Component\VarDumper\Dumper\ContextProvider\RequestContextProvider;

$manager->addContextProvider('request', new RequestContextProvider());
```

Once at least one dumper is registered, call the `laler()` helper to send values through your configured pipeline.

Use the global helper wherever you need to forward values:

```php
laler('first value', ['second' => 'value']);
```

This clones each argument and sends it through the same registered dumpers and context providers as Laravel's native dumper.

### Example integrations

You can forward dumps to any channel you control by registering the appropriate dumper. For example, the included `TauriDumper` streams values to the Windows desktop app:

```php
use Laler\DumpCaptureManager;
use Laler\Dumpers\TauriDumper;

// For Laravel projects
$manager = app(DumpCaptureManager::class);
$manager->register(new TauriDumper('http://localhost:3000'));

laler('Hello from Laravel!');
```

This will send your dumps directly to the Windows desktop app for visual debugging.

See `examples/plain_php_usage.php` for a complete plain PHP usage example, including Tauri integration helpers.

## Testing
```bash
vendor/bin/phpunit --stop-on-failure
```

## Release Process
```bash
# Install npm dependencies for release tooling
npm install

# Create a new release (automatically determines version bump)
npm run release

# Or specify version bump type
npm run release:patch   # 1.0.1 -> 1.0.2
npm run release:minor   # 1.0.1 -> 1.1.0
npm run release:major   # 1.0.1 -> 2.0.0
```

## Contributing
- **Commit format:** Use conventional commits (`feat:`, `fix:`, `docs:`, etc.)
- **Interactive commits:** Run `npm run commit` for guided commit creation
- Follow the existing code style
- Remove unused imports before committing
- Run tests before submitting

## License
MIT
