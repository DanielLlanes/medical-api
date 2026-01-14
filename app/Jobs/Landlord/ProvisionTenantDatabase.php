<?php

namespace App\Jobs\Landlord;

use Throwable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\Multitenancy\Jobs\NotTenantAware;
use App\Models\Landlord\Tenant;
use App\Mail\Landlord\VerifyTenantMail;

class ProvisionTenantDatabase implements ShouldQueue, NotTenantAware
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;

    protected Tenant $tenant;

    public function __construct(Tenant $tenant)
    {
        // Evita problemas si el modelo cambia entre reintentos
        $this->tenant = $tenant->fresh();
    }

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
            $this->activateTenant();
            $this->sendVerificationEmail();

            Log::info("✅ Tenant provisionado correctamente", [
                'tenant_id' => $this->tenant->id,
            ]);
        } catch (Throwable $e) {
            $this->handleFailure($e);
            throw $e; // Permite reintentos automáticos
        }
    }

    /**
     * ------------------------
     * Acciones
     * ------------------------
     */

    protected function createDatabase(): void
    {
        DB::statement(sprintf(
            'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
            $this->tenant->database
        ));
    }

    protected function runMigrations(): void
    {
        Artisan::call('tenants:artisan', [
            'artisanCommand' => 'migrate --database=tenant --path=database/migrations/tenant --force',
            '--tenant'       => $this->tenant->id,
        ]);
    }

    protected function runSeeders(): void
    {
        Artisan::call('tenants:artisan', [
            'artisanCommand' => 'db:seed --database=tenant --class=DatabaseSeeder --force',
            '--tenant'       => $this->tenant->id,
        ]);
    }

    protected function activateTenant(): void
    {
        $this->tenant->update([
            'status'       => 'active',
            'provisioned_at' => now(),
        ]);
    }

    protected function sendVerificationEmail(): void
    {
        Mail::to($this->tenant->email)
            ->queue(new VerifyTenantMail($this->tenant));
    }

    /**
     * ------------------------
     * Error handling
     * ------------------------
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
