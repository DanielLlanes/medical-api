<?php


use App\Http\Controllers\Landing\FaqController;
use App\Http\Controllers\Landing\PlanController;
use App\Http\Controllers\Landing\TenantVerificationController;
use App\Models\Landlord\Faq;
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
    Route::get('verify-account/{tenant}', TenantVerificationController::class)
        ->middleware('signed')                   // Link firmado
        ->name('verify');                        // Route name completo: public.tenants.verify

});

Route::get('/v1/plans-list', [PlanController::class, 'index']);
Route::get('/v1/faqs-list', [FaqController::class, 'index']);
