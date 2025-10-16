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

        if (!$this->shouldInject($response)) {
            $this->recorder->flush();

            return $response;
        }

        $messages = $this->recorder->flush();

        if ($messages === []) {
            return $response;
        }

        $payload = json_encode($messages, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($payload === false) {
            return $response;
        }

        $script = '<script>(function(){const messages=' . $payload . ';messages.forEach(function(message){console.log(message);});})();</script>';

        $content = $response->getContent();

        if ($content === false) {
            return $response;
        }

        if (str_contains($content, '</body>')) {
            $content = str_replace('</body>', $script . '</body>', $content);
        } else {
            $content .= $script;
        }

        $response->setContent($content);

        return $response;
    }

    private function shouldInject(Response $response): bool
    {
        if ($response->isRedirection()) {
            return false;
        }

        $contentType = $response->headers->get('Content-Type');

        return $contentType !== null && str_contains($contentType, 'text/html');
    }
}
