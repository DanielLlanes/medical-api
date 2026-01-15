<?php

namespace App\Observers\Landlord;

use App\Models\Landlord\Tenant;
use App\Jobs\Landlord\ProvisionTenantDatabase;
use App\Mail\Landlord\VerifyTenantMail; // Importante
use Illuminate\Support\Facades\Mail;   // Importante
use Illuminate\Support\Facades\Log;

class TenantObserver
{
    public function created(Tenant $tenant): void
    {
        $this->createBusinessProfile($tenant);
        $this->createTrialSubscription($tenant);

        if (config('custom.create_tenant_on_registration')) {
            // Flujo Directo: El Job se encarga de crear DB y mandar mail
            $this->provisionTenantDatabase($tenant);
        } else {
            // Flujo Diferido: Como el Job no corre aún, mandamos el mail de verificación AQUÍ
            Mail::to($tenant->email)->send(new VerifyTenantMail($tenant));
            
            Log::info("📧 Mail de verificación enviado desde Observer para Tenant: {$tenant->id}");
        }
    }

    protected function createBusinessProfile(Tenant $tenant): void
    {
        $tenant->businessProfile()->create([
            'specialty' => 'General',
            'is_active' => true,
        ]);
    }

    protected function createTrialSubscription(Tenant $tenant): void
    {
        $tenant->subscription()->create([
            'gateway'       => 'mercadopago',
            'status'        => 'trialing',
            'trial_ends_at' => now()->addDays(14),
            'is_active'     => true,
        ]);
    }

    protected function provisionTenantDatabase(Tenant $tenant): void
    {
        ProvisionTenantDatabase::dispatch($tenant)->afterCommit();
    }
}
