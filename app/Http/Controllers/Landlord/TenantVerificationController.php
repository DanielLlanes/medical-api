<?php

namespace App\Http\Controllers\Landlord;

use Illuminate\Http\Request;
use App\Models\Landlord\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

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

            // 2. Si ya fue verificado
            if ($tenant->verified_at !== null) {
                return $this->sendResponse(
                    ['domain' => $tenant->domain],
                    'Esta cuenta ya ha sido verificada anteriormente.'
                );
            }

            // 3. Activamos la cuenta
            $tenant->update([
                'email_verified_at' => Carbon::now(),
                'status'      => 'active',
                'is_active'   => true,
            ]);

            // Datos para el retorno
            $data = [
                'name'         => $tenant,
                'domain'       => $tenant->domain,
                'active_since' => $tenant->email_verified_at,
                'now' => now()
            ];

            return $this->sendResponse($data, '¡Cuenta verificada y activada exitosamente!');

        } catch (\Exception $e) {
            return $this->sendError('Ocurrió un error al verificar la cuenta.', $e->getMessage(), 500);
        }
    }
}