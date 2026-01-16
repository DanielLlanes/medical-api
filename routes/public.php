<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Landlord\TenantVerificationController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
| Contexto: Rutas públicas del sistema
| No requieren autenticación
| Prefijo aplicado en bootstrap/app.php: /api/v1
*/

Route::name('public.tenants.')->group(function () {

    // Verificación de cuenta del tenant (link firmado)
    Route::get(
        'verify-account/{tenant}',
        TenantVerificationController::class
    )
    ->middleware('signed')
    ->name('verify');

});