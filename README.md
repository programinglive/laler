# Laler

Capture PHP `VarDumper` output and redirect it to any channel you control. Works with or without Laravel.

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

You can forward dumps to any channel you control by registering the appropriate dumper. For example, the included `TauriDumper` streams values to a Tauri desktop app API endpoint:

```php
use Laler\DumpCaptureManager;
use Laler\Dumpers\TauriDumper;

$manager = app(DumpCaptureManager::class);
$manager->register(new TauriDumper('http://localhost:3000'));

laler('Hello from Laravel!');
```

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
