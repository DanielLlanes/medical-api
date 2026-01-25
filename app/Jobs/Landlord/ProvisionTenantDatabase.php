<?php

namespace App\Jobs\Landlord;

use Throwable;
use Illuminate\Bus\Queueable;
use App\Models\Landlord\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\Multitenancy\Jobs\NotTenantAware;
use Illuminate\Support\Facades\Artisan;

// Mails y Modelos
use App\Mail\Landlord\TenantDatabaseReadyMail;
use App\Mail\Landlord\VerifyTenantMail;
use App\Models\Tenant\User;

class ProvisionTenantDatabase implements ShouldQueue, NotTenantAware
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;
    protected Tenant $tenant;

    public function __construct(Tenant $tenant)
    {
        // Usamos fresh para asegurar que traemos el plan_id y company actualizados
        $this->tenant = $tenant->fresh();
    }

    public function handle(): void
    {
        // Cambiamos el Log para usar la Company, es más fácil de identificar
        Log::info("🚀 Iniciando provisión para clínica: {$this->tenant->company}");

        try {
            $this->createDatabase();
            $this->provisionDatabase();
            $this->createAdminUser();
            $this->activateTenant();
            $this->sendTenantEmails();

            Log::info("✅ Entorno listo para el dominio: {$this->tenant->domain}");
        } catch (Throwable $e) {
            $this->handleFailure($e);
            throw $e;
        }
    }

    protected function createDatabase(): void
    {
        // Se mantiene igual, es correcto
        DB::statement(sprintf(
            'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
            $this->tenant->database
        ));
    }

    protected function provisionDatabase(): void
    {
        // Migraciones y Seeders (Se mantiene igual, es correcto)
        Artisan::call('tenants:artisan', [
            'artisanCommand' => 'migrate --database=tenant --path=database/migrations/tenant --force',
            '--tenant'       => $this->tenant->id,
        ]);

        Artisan::call('tenants:artisan', [
            'artisanCommand' => 'db:seed --database=tenant --class=DatabaseSeeder --force',
            '--tenant'       => $this->tenant->id,
        ]);
    }

    protected function createAdminUser(): void
    {
        $this->tenant->makeCurrent();
        $adminData = $this->tenant->setup_data;

        // Aseguramos que use el admin_name que ahora mandamos desde el controlador
        User::updateOrCreate(
            ['email' => $adminData['admin_email']],
            [
                'name'      => $adminData['admin_name'] ?? $this->tenant->name,
                'password'  => $adminData['admin_password'],
                'is_active' => true,
            ]
        );

        $this->tenant->forgetCurrent();
    }

    protected function activateTenant(): void
    {
        $this->tenant->update([
            'status'         => 'active',
            'provisioned_at' => now(),
        ]);
    }

    protected function sendTenantEmails(): void
    {
        // Lógica de correos según el flujo configurado

        // 1. Flujo DIFERIDO: El usuario ya verificó su mail y la DB se acaba de crear.
        if ($this->tenant->email_verified_at !== null && !config('custom.create_tenant_on_registration')) {
            Mail::to($this->tenant->email)->queue(new TenantDatabaseReadyMail($this->tenant));
            Log::info("📧 Enviado: TenantDatabaseReadyMail (Clínica lista para usar)");
            return;
        }

        // 2. Flujo ON-THE-FLY: Se acaba de registrar, la DB ya está lista, ahora debe verificar su mail.
        if (config('custom.create_tenant_on_registration') && $this->tenant->email_verified_at === null) {
            Mail::to($this->tenant->email)->queue(new VerifyTenantMail($this->tenant));
            Log::info("📧 Enviado: VerifyTenantMail (Esperando verificación del Dr.)");
        }
    }

    protected function handleFailure(Throwable $e): void
    {
        Log::error("❌ ERROR CRÍTICO en Tenant {$this->tenant->id}: {$e->getMessage()}");
        $this->tenant->update(['status' => 'error']);
    }
}
