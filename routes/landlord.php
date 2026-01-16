<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Landlord\TenantController;

/*
|--------------------------------------------------------------------------
| LANDLORD ROUTES
|--------------------------------------------------------------------------
| Contexto: Administración del sistema
| Middleware aplicado en bootstrap/app.php
| Prefijo aplicado: /api/v1
*/

Route::name('landlord.tenants.')->group(function () {

    // Listar tenants
    Route::get('tenants', [TenantController::class, 'index'])
        ->name('list');

    // Crear tenant
    Route::post('tenants', [TenantController::class, 'store'])
        ->name('store');

    // Ver tenant
    Route::get('tenants/{tenant}', [TenantController::class, 'show'])
        ->name('show');

    // Actualizar tenant
    Route::put('tenants/{tenant}', [TenantController::class, 'update'])
        ->name('update');

    // Eliminar tenant
    Route::delete('tenants/{tenant}', [TenantController::class, 'destroy'])
        ->name('destroy');

    // Acción especial (ejemplo estilo monolito)
    Route::post('tenants/status', [TenantController::class, 'changeStatus'])
        ->name('status');
});