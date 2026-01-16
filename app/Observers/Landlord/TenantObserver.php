<?php

namespace App\Observers\Landlord;

use App\Models\Landlord\Tenant;
use App\Jobs\Landlord\ProvisionTenantDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\Landlord\VerifyTenantMail;
use Illuminate\Support\Facades\Log;

class TenantObserver
{
    public function created(Tenant $tenant): void
    {
        // 1️⃣ Preparar datos básicos (Landlord)
        $this->createBusinessProfile($tenant);
        $this->createTrialSubscription($tenant);

        // 2️⃣ Decidir comunicación según configuración
        if (config('custom.create_tenant_on_registration')) {
            // SI ES ON-THE-FLY: El Job hará TODO (crear DB y notificar)
            ProvisionTenantDatabase::dispatch($tenant)->afterCommit();
            Log::info("🚀 Job de aprovisionamiento disparado (on-the-fly) para Tenant {$tenant->id}");
        } else {
            // SI ES DIFERIDO: Solo enviamos verificación. La DB se creará tras verificar.
            Mail::to($tenant->email)->queue(new VerifyTenantMail($tenant));
            Log::info("📧 Mail de verificación enviado (flujo diferido) para Tenant {$tenant->id}");
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
}