<?php

use App\Http\Controllers\Landlord\PlanController;
use App\Http\Controllers\Public\TenantVerificationController;
use Illuminate\Support\Facades\Route;



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
    Route::get('verify-account/{tenant}',TenantVerificationController::class)
    ->middleware('signed')                   // Link firmado
    ->name('verify');                        // Route name completo: public.tenants.verify

});

Route::get('/v1/plans-list', [PlanController::class, 'getActivePlans']);
