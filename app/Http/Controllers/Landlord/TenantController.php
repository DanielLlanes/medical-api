<?php

namespace App\Http\Controllers\Landlord;

use App\Models\Landlord\Plan;
use App\Models\Landlord\Tenant;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantNamingHelper;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Landlord\Tenant\StoreTenantRequest;
use App\Traits\ApiResponseTrait;

class TenantController extends BaseLandlordController
{
    use ApiResponseTrait;

    /**
     * Crear un tenant nuevo (Clínica).
     */
    public function store(StoreTenantRequest $request)
    {
        // 1. Iniciamos la transacción para asegurar que el Tenant y sus relaciones (vía Observer) se creen bien
        $tenant = DB::transaction(function () use ($request) {
            return $this->createTenant($request);
        });

        // 2. Definimos el mensaje según el flujo configurado (On-the-fly o Diferido)
        $message = config('custom.create_tenant_on_registration')
            ? '¡Bienvenido! Estamos configurando tu clínica, en unos minutos recibirás un correo.'
            : 'Registro exitoso. Por favor verifica tu correo para activar tu clínica.';

        return $this->sendResponse(
            $tenant->toArray(),
            $message,
            201
        );
    }

    /**
     * Lógica de creación del Tenant en Landlord
     */
    protected function createTenant(StoreTenantRequest $request): Tenant
    {
        // Buscamos el plan enviado desde la Landing (basico, pro, etc)
        $plan = Plan::where('slug', $request->plan_id)
                    ->where('is_active', true)
                    ->firstOrFail();

        // LOG de depuración
\Log::info('🔍 [API Register] Plan encontrado:', [
    'slug_buscado' => $request->plan_id,
    'plan_id_bd'   => $plan->id,
    'plan_nombre'  => $plan->name,
    'trial_days'   => $plan->trial_days
]);

        // Generamos el subdominio limpio basado en la COMPANY
        $slug = TenantNamingHelper::generateSubdomain($request->company);
        $fullDomain = $slug . '.' . config('custom.base_domain');

        return Tenant::create([
            'name'      => $request->name,    // El nombre del Doctor
            'email'     => $request->email,   // Email del Doctor
            'company'   => $request->company, // Nombre de la Clínica (Asegúrate que exista en tu migración)
            'domain'    => $fullDomain,       // daniel.medical.test
            'database'  => TenantNamingHelper::generateDatabaseName($request->company),
            'plan_id'   => $plan->id,
            'setup_data'=> [
                'admin_name'     => $request->name,
                'admin_email'    => $request->email,
                'admin_password' => Hash::make($request->password), // El Job lo usará para el Admin en la DB Tenant

            ],
        ]);
    }
}
