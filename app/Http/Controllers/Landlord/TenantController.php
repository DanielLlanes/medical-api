<?php

namespace App\Http\Controllers\Landlord;

use Illuminate\Support\Str;
use App\Models\Landlord\Plan;
use App\Models\Landlord\Tenant;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantNamingHelper;
use App\Http\Requests\Landlord\Tenant\StoreTenantRequest;

class TenantController extends BaseLandlordController
{
    public function store(StoreTenantRequest $request)
    {
        // Guardamos el tenant en una variable para retornarlo
        $tenant = DB::transaction(function () use ($request) {
            $plan = Plan::where('slug', 'basico')->where('is_active', true)->firstOrFail();

            return Tenant::create([
                'name'     => $request->name,
                'domain'   => TenantNamingHelper::generateSubdomain($request->name),
                'database' => TenantNamingHelper::generateDatabaseName($request->name), 
                'plan_id'  => $plan->id,
                'is_active'=> false,
            ]);
        });

        // IMPORTANTE: Retornar la respuesta al cliente
        return response()->json([
            'message' => 'Tenant registrado con éxito. La base de datos se está aprovisionando.',
            'data'    => $tenant
        ], 201);
    }
}