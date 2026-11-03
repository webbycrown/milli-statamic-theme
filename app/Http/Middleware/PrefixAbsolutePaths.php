<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PrefixAbsolutePaths
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $base = rtrim((string) config('app.url'), '/');
        $prefix = (string) (parse_url($base, PHP_URL_PATH) ?: '');
        if ($prefix === '' || $prefix === '/') {
            return $response;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');
        if ($contentType !== '' && ! str_contains($contentType, 'text/html')) {
            return $response;
        }

        $html = $response->getContent();
        if (! is_string($html) || $html === '') {
            return $response;
        }

        $html = preg_replace_callback(
            '#\b(href|src|action)=("|\')\s*/(?!/)([^"\']*?)\s*\2#i',
            function (array $m) use ($prefix) {
                $path = '/'.ltrim(trim($m[3]), '/');
                if (str_starts_with($path, $prefix.'/') || $path === $prefix) {
                    return $m[1].'='.$m[2].$path.$m[2];
                }

                return $m[1].'='.$m[2].$prefix.$path.$m[2];
            },
            $html
        ) ?? $html;

        $response->setContent($html);

        return $response;
    }
}
