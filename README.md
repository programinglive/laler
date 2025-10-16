# Laler

Capture Laravel `VarDumper` output and redirect it to any channel you control.

## Requirements
- PHP ^8.0
- Laravel (Illuminate components) ^9.0 || ^12.0
- Symfony VarDumper ^6.0 || ^7.0

## Installation
```bash
composer require laler/laler
```
The package is auto-discovered by Laravel, so no manual provider registration is required.

## Usage
`DumpCaptureManager` centralises dump collection and forwards each value to your registered dumpers. Retrieve it from the container and register any `DataDumperInterface` implementation:

```php
use Laler\DumpCaptureManager;
use Symfony\Component\VarDumper\Dumper\CliDumper;

$manager = app(DumpCaptureManager::class);
$manager->register(new CliDumper());

dump('Hello from Laler!'); // Routed to the CLI dumper
```

Add extra context to every dump by registering `ContextProviderInterface` implementations:

```php
use Symfony\Component\VarDumper\Dumper\ContextProvider\RequestContextProvider;

$manager->addContextProvider('request', new RequestContextProvider());
```

Once at least one dumper is registered, the manager swaps Laravel's default `VarDumper` handler so all subsequent `dump()` calls flow through your configured pipeline.

When you want to forward values without calling `dump()`, use the global helper:

```php
laler('first value', ['second' => 'value']);
```

This clones each argument and sends it through the same registered dumpers and context providers as Laravel's native dumper.

### Browser console output

The package automatically registers a browser console dumper. After installation you can call `laler()` (or `dump()`) anywhere in your web request lifecycle—such as inside a route:

```php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    laler('Hello World');

    return view('welcome');
});
```

When the response is HTML, a small script is injected just before `</body>` and every message is forwarded to `console.log` in your browser's developer tools.

## Testing
```bash
vendor/bin/phpunit --stop-on-failure
```

## Contributing
- Follow the existing code style
- Remove unused imports before committing
- Avoid using the `any` type in TypeScript contributions

## License
MIT
