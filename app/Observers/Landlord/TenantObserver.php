<?php

namespace App\Observers\Landlord;

use Illuminate\Support\Str;
use App\Models\Landlord\Tenant;
use App\Jobs\Landlord\ProvisionTenantDatabase;

class TenantObserver
{
    /**
     * Handle the Tenant "created" event.
     */
    public function created(Tenant $tenant): void
    {
        // 1. Creamos el Perfil de Negocio (vacío para que no aburra al usuario)
        // Usamos la relación definida en el modelo
        $tenant->businessProfile()->create([
            'specialty' => 'General', // Valor por defecto
            'is_active' => true,
            // Los campos tax_id, legal_name, etc., se quedan null por ahora
        ]);

        // 2. Creamos la Suscripción inicial (Trial)
        $tenant->subscription()->create([
            'gateway'    => 'mercadopago',
            'status'     => 'trialing',
            'trial_ends_at' => now()->addDays(14), // 14 días de prueba
            'is_active'  => true,
        ]);

        // 3. ¡EL PASO CLAVE! Lanzamos el Job para crear la DB física
        // Esto se va a la tabla 'jobs' y se ejecuta en segundo plano
        ProvisionTenantDatabase::dispatch($tenant)->afterCommit();
    }

    /**
     * Handle the Tenant "updated" event.
     */
    public function updated(Tenant $tenant): void
    {
        //
    }

    /**
     * Handle the Tenant "deleted" event.
     */
    public function deleted(Tenant $tenant): void
    {
        //
    }

    /**
     * Handle the Tenant "restored" event.
     */
    public function restored(Tenant $tenant): void
    {
        //
    }

    /**
     * Handle the Tenant "force deleted" event.
     */
    public function forceDeleted(Tenant $tenant): void
    {
        //
    }
}
