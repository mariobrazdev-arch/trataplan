<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->path();

        if ($path === 'en' || str_starts_with($path, 'en/')) {
            App::setLocale('en');
        } else {
            App::setLocale('pt');
        }

        return $next($request);
    }
}
