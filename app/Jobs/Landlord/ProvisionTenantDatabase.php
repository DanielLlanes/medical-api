<?php

namespace App\Jobs\Landlord;

use Illuminate\Bus\Queueable;
use App\Models\Landlord\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use App\Mail\Landlord\VerifyTenantMail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\Multitenancy\Jobs\NotTenantAware;

class ProvisionTenantDatabase implements ShouldQueue, NotTenantAware
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tenant;

    /**
     * El Job recibe el modelo del Tenant
     */
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * Aquí ocurre la magia
     */
    public function handle(): void
    {
        try {
            // 1. Crear la base de datos física en MySQL
            // Usamos el nombre que guardamos en la tabla tenants
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$this->tenant->database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

            // 2. Ejecutar las migraciones del Tenant
            // CORRECCIÓN: Usar la sintaxis correcta de tenants:artisan
            Artisan::call('tenants:artisan', [
                'artisanCommand' => 'migrate --database=tenant --path=database/migrations/tenant', 
                '--tenant' => $this->tenant->id,
            ]);

            // 3. Opcional: Ejecutar Seeders básicos para el nuevo cliente
            Artisan::call('tenants:artisan', [
                'artisanCommand' => 'db:seed --database=tenant --class=DatabaseSeeder', 
                '--tenant' => $this->tenant->id,
            ]);

            // 4. Actualizar el estado del Tenant a 'active'
            $this->tenant->update([
                'status' => 'active'
            ]);

            Log::info("Base de datos creada y migrada para el Tenant: {$this->tenant->name}");

            Mail::to($this->tenant->email)->send(new VerifyTenantMail($this->tenant));

        } catch (\Exception $e) {
            Log::error("Error aprovisionando la DB para el Tenant {$this->tenant->id}: " . $e->getMessage());
            
            $this->tenant->update([
                'status' => 'suspended' // O un estado de 'error' para que tú revises
            ]);

            throw $e; // Reintenta el job si falla
        }
    }
}