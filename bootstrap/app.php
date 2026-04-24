<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            '/api/track',
            '/api/track/duration',
        ]);

        $middleware->web(prepend: [
            \App\Http\Middleware\RedirectTrailingSlash::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function (mixed $response, \Throwable $e, Request $request) {
            $status = $response->getStatusCode();

            // Não intercepta rotas do admin nem requisições Inertia internas
            if (
                $request->is('admin*') ||
                $request->is('up') ||
                ($request->header('X-Inertia') && $status < 500)
            ) {
                return $response;
            }

            $renderStatuses = [404, 410, 403, 419, 429, 500, 503];

            if (in_array($status, $renderStatuses) || ($status >= 500)) {
                return Inertia::render('Error', ['status' => $status])
                    ->toResponse($request)
                    ->setStatusCode($status);
            }

            return $response;
        });
    })->create();
