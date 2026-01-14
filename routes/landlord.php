<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Landlord\TenantController;

/*
|--------------------------------------------------------------------------
| Landlord Routes (System Administration)
|--------------------------------------------------------------------------
|
| Estas rutas se cargan con el prefijo 'api/landlord' y NO requieren 
| el middleware 'tenant' porque su propósito es gestionar el sistema global.
|
*/

Route::prefix('v1')->group(function () {

    // Gestión de Clínicas (Tenants)
    Route::apiResource('tenants', TenantController::class);
    
    /* Al usar apiResource, ya tienes:
       POST /api/landlord/v1/tenants          -> store
       GET  /api/landlord/v1/tenants          -> index
       GET  /api/landlord/v1/tenants/{id}     -> show
       PUT  /api/landlord/v1/tenants/{id}     -> update
       DELETE /api/landlord/v1/tenants/{id}  -> destroy
    */

});