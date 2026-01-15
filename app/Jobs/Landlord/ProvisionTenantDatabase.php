<?php

namespace App\Jobs\Landlord;

use Throwable;
use Illuminate\Bus\Queueable;
use App\Models\Landlord\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use App\Mail\Landlord\VerifyTenantMail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

// Modelos
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\Multitenancy\Jobs\NotTenantAware;

// Mails
use App\Mail\Landlord\TenantDatabaseReadyMail;
use App\Models\Tenant\User; // El modelo del Tenant con el trait de conexión
// use App\Mail\Landlord\TenantReadyMail; // Descomentar cuando crees este mail

class ProvisionTenantDatabase implements ShouldQueue, NotTenantAware
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;

    protected Tenant $tenant;

    /**
     * Crear una nueva instancia del Job.
     */
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant->fresh();
    }

    /**
     * Ejecutar el Job.
     */
    public function handle(): void
    {
        Log::info("🚀 Iniciando provisión del Tenant", [
            'tenant_id' => $this->tenant->id,
            'database'  => $this->tenant->database,
        ]);

        try {
            $this->createDatabase();
            $this->runMigrations();
            $this->runSeeders();
            
            // Creamos al usuario administrador en la nueva DB
            $this->createAdminUser();

            // Marcamos como activo y provisionado
            $this->activateTenant();

            // Enviamos la notificación correspondiente
            $this->handleNotification();

            Log::info("✅ Tenant provisionado correctamente", [
                'tenant_id' => $this->tenant->id,
            ]);
        } catch (Throwable $e) {
            $this->handleFailure($e);
            throw $e;
        }
    }

    /**
     * Crea la base de datos física.
     */
    protected function createDatabase(): void
    {
        DB::statement(sprintf(
            'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
            $this->tenant->database
        ));
    }

    /**
     * Ejecuta las migraciones en la base de datos del tenant.
     */
    protected function runMigrations(): void
    {
        Artisan::call('tenants:artisan', [
            'artisanCommand' => 'migrate --database=tenant --path=database/migrations/tenant --force',
            '--tenant'       => $this->tenant->id,
        ]);
    }

    /**
     * Ejecuta los seeders (si existen) para el tenant.
     */
    protected function runSeeders(): void
    {
        Artisan::call('tenants:artisan', [
            'artisanCommand' => 'db:seed --database=tenant --class=DatabaseSeeder --force',
            '--tenant'       => $this->tenant->id,
        ]);
    }

    /**
     * Crea el primer usuario administrador usando los datos de registro.
     */
    protected function createAdminUser(): void
    {
        // Switch a la conexión del tenant
        $this->tenant->makeCurrent();

        $adminData = $this->tenant->setup_data;

        User::create([
            'name'      => $adminData['admin_name'] ?? $this->tenant->name,
            'email'     => $adminData['admin_email'],
            'password'  => $adminData['admin_password'], // Ya viene hasheado
            'is_active' => true,
        ]);

        // Regresar a la conexión landlord
        $this->tenant->forgetCurrent();
    }

    /**
     * Decide qué mail enviar según el estado de verificación.
     */
    protected function handleNotification(): void
    {
        if ($this->tenant->email_verified_at === null) {
            // Caso: Registro con creación inmediata (necesita verificar)
            Mail::to($this->tenant->email)->queue(new VerifyTenantMail($this->tenant));
        } else {
            
           Mail::to($this->tenant->email)->queue(new TenantDatabaseReadyMail($this->tenant));
        
            Log::info("📧 Mail de 'Instancia Operativa' enviado a: {$this->tenant->email}");
        }
    }

    /**
     * Actualiza el estado del Tenant en el Landlord.
     */
    protected function activateTenant(): void
    {
        $this->tenant->update([
            'status'         => 'active',
            'provisioned_at' => now(),
        ]);
    }

    /**
     * Gestión de errores.
     */
    protected function handleFailure(Throwable $e): void
    {
        Log::error("❌ Error aprovisionando Tenant", [
            'tenant_id' => $this->tenant->id,
            'error'     => $e->getMessage(),
        ]);

        $this->tenant->update([
            'status' => 'error',
        ]);
    }
}