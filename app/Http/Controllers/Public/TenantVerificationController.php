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
use Illuminate\Support\Facades\DB; // Importante para asegurar el commit

class TenantVerificationController extends BaseLandlordController
{
    use ApiResponseTrait;

    public function __invoke(Request $request, $tenantId)
    {

        // Cargamos relaciones para evitar queries extras
        $tenant = Tenant::with(['subscription', 'plan'])->findOrFail($tenantId);

        if ($tenant->email_verified_at !== null) {
            return $this->sendResponse([
                'urls' => ['login' => "https://{$tenant->domain}/login"]
            ], 'Esta cuenta ya ha sido verificada.');
        }

        try {
            // Usamos una transacción explícita para asegurar que los datos se guarden ANTES de disparar el Job
            DB::transaction(function () use ($tenant) {
                // 1. Verificamos y activamos en Landlord
                $tenant->update([
                    'email_verified_at' => Carbon::now(),
                    'is_active'         => true,
                    'status'            => 'active'
                ]);

                // 2. Reiniciamos el Trial
                if ($tenant->subscription && $tenant->plan) {
                    $tenant->subscription->update([
                        'trial_ends_at' => Carbon::now()->addDays($tenant->plan->trial_days ?? 14),
                        'status'        => 'trialing'
                    ]);
                }
            });

            // Forzamos el refresco del modelo DESPUÉS de la transacción
            $tenant->refresh();

            // --- 3. ORQUESTACIÓN ---
            if (config('custom.create_tenant_on_registration')) {
                // Modo On-the-fly: DB ya existe.
                // Usamos queue() aquí porque no hay un Job pesado corriendo.
                Mail::to($tenant->email)->queue(new TenantDatabaseReadyMail($tenant));
                $message = '¡Cuenta verificada y clínica lista!';
            } else {
                // Modo Diferido: La DB NO existe.
                // IMPORTANTE: afterCommit() asegura que el Job solo empiece
                // cuando MySQL haya confirmado que email_verified_at ya NO es null.
                ProvisionTenantDatabase::dispatch($tenant)->afterCommit();

                $message = '¡Cuenta verificada! Estamos preparando tu entorno médico, te avisaremos por correo.';
            }

            return $this->sendResponse([
                'tenant' => $tenant->makeHidden(['subscription', 'plan']),
                'urls'   => ['login' => "https://{$tenant->domain}/login"]
            ], $message);

        } catch (\Exception $e) {
            Log::error("❌ Error en verificación de Tenant {$tenantId}: " . $e->getMessage());
            return $this->sendError('Error al procesar la verificación.', 500);
        }
    }
}
