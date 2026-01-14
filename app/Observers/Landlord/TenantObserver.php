<?php

namespace App\Observers\Landlord;

use App\Models\Landlord\Tenant;
use App\Jobs\Landlord\ProvisionTenantDatabase;

class TenantObserver
{
    public function created(Tenant $tenant): void
    {
        $this->createBusinessProfile($tenant);
        $this->createTrialSubscription($tenant);
        $this->provisionTenantDatabase($tenant);
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
