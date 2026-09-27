<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventStaleSessionPages
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        if ($request->is('login', 'dashboard', 'admin/*', 'guru/*', 'kepsek/*')) {
            $response->headers->set('Cache-Control', 'no-store, private, max-age=0');
        }

        return $response;
    }
}
