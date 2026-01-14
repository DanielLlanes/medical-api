<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Landlord\TenantController;
use App\Http\Controllers\Landlord\TenantVerificationController;

/*
|--------------------------------------------------------------------------
| Landlord Routes (System Administration)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Gestión de Clínicas (Tenants)
    Route::apiResource('tenants', TenantController::class);

    /**
     * Ruta para la verificación de cuenta
     * La URL será: /api/landlord/v1/verify-account/{tenant}?expires=...&signature=...
     */
    Route::get('/verify-account/{tenant}', TenantVerificationController::class)
    ->name('tenant.verify')
    ->middleware('signed');

});