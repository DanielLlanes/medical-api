<?php

namespace App\Http\Controllers\Landlord;

use App\Models\Landlord\Plan;
use App\Models\Landlord\Tenant;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantNamingHelper;
use App\Http\Requests\Landlord\Tenant\StoreTenantRequest;

class TenantController extends BaseLandlordController
{
    public function store(StoreTenantRequest $request)
    {
        $tenant = DB::transaction(function () use ($request) {
            return $this->createTenant($request);
        });

        return $this->sendResponse(
            $tenant,
            'Tenant registrado con éxito. La base de datos se está aprovisionando.',
            201
        );
    }

    /**
     * ------------------------
     * Acciones
     * ------------------------
     */

    protected function createTenant(StoreTenantRequest $request): Tenant
    {
        $plan = $this->getDefaultPlan();

        return Tenant::create([
            'name'      => $request->name,
            'email'      => $request->email,
            'domain'    => TenantNamingHelper::generateSubdomain($request->name),
            'database'  => TenantNamingHelper::generateDatabaseName($request->name),
            'plan_id'   => $plan->id,
        ]);
    }

    protected function getDefaultPlan(): Plan
    {
        return Plan::where('slug', 'basico')
            ->where('is_active', true)
            ->firstOrFail();
    }
}
