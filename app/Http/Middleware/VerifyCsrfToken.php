<?php

namespace App\Http\Middleware;

use Closure;
use Imarc\Millyard\Contracts\MiddlewareInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyCsrfToken implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('DELETE')) {
            $csrfToken = $request->headers->get('X-XSRF-TOKEN');
            if (! $csrfToken || ! wp_verify_nonce($csrfToken, csrf_token_key())) {

                return new Response('Unauthorized', 401);
            }
        }

        return $next($request);
    }
}
