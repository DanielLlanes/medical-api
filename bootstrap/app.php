<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Rutas para el Administrador del Sistema (Landlord)
            Route::middleware('api')
                ->prefix('landlord')
                ->group(base_path('routes/landlord.php'));

            // Rutas para las Clínicas (Tenants)
            Route::middleware(['api', 'tenant'])
                ->prefix('api/v1')
                ->group(base_path('routes/tenant.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Creamos el grupo 'tenant' tal cual lo pide la documentación
        $middleware->group('tenant', [
            \Spatie\Multitenancy\Http\Middleware\NeedsTenant::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();