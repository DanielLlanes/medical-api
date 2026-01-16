<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',        // Rutas web (landing si aplica)
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {

            // Todas las APIs versionadas
            Route::middleware('api')
                ->prefix('api/v1')
                ->group(function () {

                    // Rutas del LANDLORD (rol global del sistema)
                    // URLs limpias, el rol se controla por middleware
                    Route::middleware('landlord')
                        ->group(base_path('routes/landlord.php'));

                    // Rutas del TENANT (requiere tenant identificado)
                    Route::middleware('tenant')
                        ->group(base_path('routes/tenant.php'));

                    // Rutas PUBLICAS (sin auth, ej: verify account)
                    Route::group(base_path('routes/public.php'));
                });
        }
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Middleware para resolver el tenant (Spatie)
        $middleware->group('tenant', [
            \Spatie\Multitenancy\Http\Middleware\NeedsTenant::class,
        ]);

        // Middleware para asegurar rol landlord
        $middleware->group('landlord', [
            \App\Http\Middleware\EnsureUserIsLandlord::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Manejo de excepciones (opcional)
    })
    ->create();