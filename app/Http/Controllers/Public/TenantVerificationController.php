<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Landlord\BaseLandlordController;
use Illuminate\Http\Request;
use App\Models\Landlord\Tenant;
use Illuminate\Support\Carbon;
use App\Jobs\Landlord\ProvisionTenantDatabase;
use App\Mail\Landlord\TenantDatabaseReadyMail;
use Illuminate\Support\Facades\Mail;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Log;

class TenantVerificationController extends BaseLandlordController
{
    use ApiResponseTrait;

    public function __invoke(Request $request, $tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);

        if ($tenant->email_verified_at !== null) {
            return $this->sendResponse([], 'Esta cuenta ya ha sido verificada.');
        }

        // 1. Verificamos al usuario
        $tenant->update([
            'email_verified_at' => Carbon::now(),
            'is_active'         => true, 
        ]);

        // 2. ¿Qué correo toca enviar?
        if (config('custom.create_tenant_on_registration')) {
            // EN TRUE: La DB ya existe. El Job ya envió la bienvenida.
            // El controlador cierra el ciclo enviando el de "Clínica Lista".
            Mail::to($tenant->email)->queue(new TenantDatabaseReadyMail($tenant));
            $message = '¡Cuenta verificada y clínica lista!';
            Log::info("📧 Enviado: TenantDatabaseReadyMail desde Controlador (Flujo On-the-fly)");
        } else {
            // EN FALSE: La DB no existe. Disparamos el Job.
            // El Job se encargará de enviar el "Clínica Lista" al terminar.
            ProvisionTenantDatabase::dispatch($tenant);
            $message = '¡Cuenta verificada! Estamos preparando tu clínica.';
            Log::info("⚙️ Job disparado desde Controlador (Flujo Diferido)");
        }

        return $this->sendResponse([
            'tenant' => $tenant,
            'urls'   => [
                'login' => "https://{$tenant->domain}/login"
            ]
        ], $message);
    }
}