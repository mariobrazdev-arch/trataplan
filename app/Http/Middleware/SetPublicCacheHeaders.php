<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetPublicCacheHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('GET') && $response->getStatusCode() === 200) {
            $response->setPublic();
            $response->setMaxAge(3600);
            $response->setSharedMaxAge(3600);
        }

        return $response;
    }
}
