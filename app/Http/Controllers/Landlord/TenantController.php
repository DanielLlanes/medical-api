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
     * Crear un tenant nuevo.
     * POST /api/landlord/v1/tenants
     */
    public function store(StoreTenantRequest $request)
    {
        $tenant = DB::transaction(function () use ($request) {
            return $this->createTenant($request);
        });

        // Ajustamos mensaje según configuración
        $message = config('custom.create_tenant_on_registration') 
            ? 'Tenant registrado. La base de datos se está aprovisionando.' 
            : 'Tenant registrado. Por favor verifica tu correo para activar tu cuenta.';

        return $this->sendResponse(
            $tenant->toArray(),
            $message,
            201
        );
    }

    /**
     * Crear tenant en la tabla Landlord
     */
    protected function createTenant(StoreTenantRequest $request): Tenant
    {
        $plan = $this->getDefaultPlan();

        return Tenant::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'domain'    => TenantNamingHelper::generateSubdomain($request->name),
            'database'  => TenantNamingHelper::generateDatabaseName($request->name),
            'plan_id'   => $plan->id,
            'setup_data'=> [
                'admin_email'    => $request->email,
                'admin_password' => Hash::make($request->password),
            ],
        ]);
    }

    /**
     * Plan por defecto
     */
    protected function getDefaultPlan(): Plan
    {
        return Plan::where('slug', 'basico')
            ->where('is_active', true)
            ->firstOrFail();
    }
}

