<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Máx 5 tentativas de login por minuto por IP+email
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->ip() . '|' . strtolower($request->input('email', '')))
                ->response(fn () => back()->withErrors(['email' => 'Muitas tentativas. Aguarde 1 minuto.']));
        });

        // Máx 3 envios de contato por IP a cada 10 minutos
        RateLimiter::for('contact', function (Request $request) {
            return Limit::perMinutes(10, 3)
                ->by($request->ip())
                ->response(fn () => back()->withErrors(['message' => 'Muitas mensagens enviadas. Tente novamente em alguns minutos.']));
        });
    }
}
