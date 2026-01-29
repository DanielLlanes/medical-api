<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',       // Rutas web comunes
        commands: __DIR__ . '/../routes/console.php', // Rutas para comandos Artisan
        health: '/up',                           // Health check endpoint
        then: function () {

            /*
            |--------------------------------------------------------------------------
            | Landlord Routes
            |--------------------------------------------------------------------------
            | Todas las rutas relacionadas con la administración del sistema
            | (gestión de tenants, planes, verificación, etc.)
            */
            Route::middleware('api')
                ->prefix('landlord')
                ->group(function () {
                    require base_path('routes/landlord.php');
                });

            /*
            |--------------------------------------------------------------------------
            | Tenant API Routes
            |--------------------------------------------------------------------------
            | Rutas para cada tenant (multi-tenant), protegidas con middleware 'tenant'
            */
            Route::middleware(['api', 'tenant'])
                ->prefix('api/v1')
                ->group(function () {
                    require base_path('routes/tenant.php');
                });

            /*
            |--------------------------------------------------------------------------
            | Public Routes
            |--------------------------------------------------------------------------
            | Landing page, login y verificación de tenants
            | Estas rutas no requieren autenticación todavía
            */
            Route::middleware('web')
                ->group(function () {
                    require base_path('routes/landing.php');
                });
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        /*
        |--------------------------------------------------------------------------
        | Tenant Middleware
        |--------------------------------------------------------------------------
        | Se asegura de que el tenant actual esté disponible en las peticiones
        */
        $middleware->group('tenant', [
            \Spatie\Multitenancy\Http\Middleware\NeedsTenant::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
