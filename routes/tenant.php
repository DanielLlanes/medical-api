<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\AuthController;

/*
|--------------------------------------------------------------------------
| TENANT ROUTES
|--------------------------------------------------------------------------
| Contexto: Rutas del tenant (clientes / clínicas)
| Middleware aplicado en bootstrap/app.php: tenant
| Prefijo aplicado en bootstrap/app.php: /api/v1
*/

Route::name('tenant.auth.')->group(function () {

    // Login del tenant (sin auth por ahora)
    Route::post('login', [AuthController::class, 'login'])
        ->name('login');

    // Placeholder para logout, register, etc.
    // Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});