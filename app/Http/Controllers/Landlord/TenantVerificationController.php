<?php

namespace App\Http\Controllers\Landlord;

use Illuminate\Http\Request;
use App\Models\Landlord\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use App\Jobs\Landlord\ProvisionTenantDatabase; // Importamos el Job

class TenantVerificationController extends BaseLandlordController
{
    /**
     * Maneja la verificación de la cuenta del Tenant.
     */
    public function __invoke(Request $request, $tenantId): JsonResponse
    {
        try {
            // 1. Buscamos al tenant
            $tenant = Tenant::find($tenantId);

            if (!$tenant) {
                return $this->sendError('El tenant no existe.', null, 404);
            }

            // 2. Si ya fue verificado (usando la columna nativa email_verified_at)
            if ($tenant->email_verified_at !== null) {
                return $this->sendResponse(
                    ['domain' => $tenant->domain],
                    'Esta cuenta ya ha sido verificada anteriormente.'
                );
            }

            // 3. Activamos la cuenta en el Landlord
            $tenant->update([
                'email_verified_at' => Carbon::now(),
                'status'            => 'active',
                'is_active'         => true,
            ]);

            // 4. ACCIÓN DIFERIDA: Si la config está en false, aprovisionamos la DB AHORA
            if (!config('custom.create_tenant_on_registration')) {
                ProvisionTenantDatabase::dispatch($tenant);
            }

            // Datos para el retorno
            $data = [
                'name'         => $tenant->name, // Solo el nombre para limpieza
                'domain'       => $tenant->domain,
                'active_since' => $tenant->email_verified_at->toDateTimeString(),
                'setup'        => config('custom.create_tenant_on_registration') ? 'already_provisioned' : 'provisioning_started'
            ];

            return $this->sendResponse($data, '¡Cuenta verificada y activada exitosamente!');

        } catch (\Exception $e) {
            return $this->sendError('Ocurrió un error al verificar la cuenta.', $e->getMessage(), 500);
        }
    }
}