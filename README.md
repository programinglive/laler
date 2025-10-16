# Laler

Capture PHP `VarDumper` output and redirect it to any channel you control. Works with or without Laravel.

## Table of Contents

- [Quick Start](#quick-start)
- [Requirements](#requirements)
- [Installation](#installation)
- [Windows Desktop App](#windows-desktop-app)
- [Integration Guides](#integration-guides)
  - [Plain PHP](#plain-php)
  - [Laravel](#laravel)
- [Usage Examples](#usage-examples)
  - [Laravel Usage](#laravel-usage)
  - [Plain PHP Usage](#plain-php-usage)
  - [Desktop App Integration](#desktop-app-integration)
- [Testing](#testing)
- [Release Process](#release-process)
- [Contributing](#contributing)
- [License](#license)

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
- **Boot autoloader:** `require 'vendor/autoload.php';`
- **Get manager:** `$manager = laler_manager();`
- **Register dumpers:** `$manager->register(new TauriDumper('http://localhost:3000'));`
- **Send values:** `laler($data);`

> **Note:** In plain PHP projects, the vendor directory is typically at your project root, so use `__DIR__ . '/vendor/autoload.php'`. In Laravel projects within subdirectories, you might need `__DIR__ . '/../vendor/autoload.php'` depending on your file location.

```php
require __DIR__.'/vendor/autoload.php';

use Laler\Dumpers\TauriDumper;

$manager = laler_manager();
$manager->register(new TauriDumper('http://localhost:3000'));

laler(['data' => ['nested' => 'structure']]);
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

- **Use helper:** call `laler()` anywhere to route values through configured dumpers.

## Usage Examples

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
For projects without Laravel, use the global helper functions. Here's the complete example from `examples/plain_php_usage.php`:

```php
<?php

declare(strict_types=1);

// Optional: Add custom helper functions
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

// Dumps now show in the desktop app
laler(['data' => ['nested' => 'structure']]);
```

### Desktop App Integration

To send dumps to the Windows desktop app, use the `TauriDumper`:

```php
use Laler\Dumpers\TauriDumper;

// Get the global manager and register the desktop app dumper
$manager = laler_manager();
$manager->register(new TauriDumper('http://localhost:3000'));

// Now all laler() calls will appear in the desktop app
laler('Debug message');
laler(['user_data' => $userData]);
```

> **Tip:** Start the desktop app first, then run your PHP code to see dumps in real-time.

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
