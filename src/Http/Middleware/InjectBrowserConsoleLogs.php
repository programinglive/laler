<?php

declare(strict_types=1);

namespace Laler\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laler\Support\BrowserConsoleRecorder;
use Symfony\Component\HttpFoundation\Response;

final class InjectBrowserConsoleLogs
{
    public function __construct(private BrowserConsoleRecorder $recorder)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (!$response instanceof Response) {
            return $response;
        }

        $this->recorder->flush();

        return $response;
    }

}
