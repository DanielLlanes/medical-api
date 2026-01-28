<?php

namespace App\Jobs\Landlord;

use App\Mail\Landlord\TenantDatabaseReadyMail;
use App\Mail\Landlord\VerifyTenantMail;
use App\Models\Landlord\Tenant;
use App\Models\Tenant\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

// Mails y Modelos
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\Multitenancy\Jobs\NotTenantAware;
use Throwable;

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
        Log::info('🔥 HANDLE DEL JOB EJECUTADO 🔥');
        // Cambiamos el Log para usar la Company, es más fácil de identificar
        Log::info("🚀 Iniciando provisión para clínica: {$this->tenant->company}");

        try {
            Log::info("--- 🏁 Iniciando Pasos de Provisión ---");

            $this->createDatabase();
            Log::info("1. ✅ Base de Datos creada");

            $this->provisionDatabase();
            Log::info("2. ✅ Migraciones y Seeds completados");

            $this->createAdminUser();
            Log::info("3. ✅ Usuario Admin creado en el Tenant");

            $this->activateTenant();
            Log::info("4. ✅ Tenant marcado como 'active' en Landlord");

            $this->sendTenantEmails();
            Log::info("5. ✅ Proceso de emails finalizado");

            Log::info("--- 🏁 Fin de Provisión con Éxito ---");

            Log::info("✅ Entorno listo para el dominio ->: {$this->tenant->domain}");
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
        Log::info('🔥 createAdminUser 🔥');
        User::updateOrCreate(
            ['email' => $adminData['admin_email']],
            [
                'name'      => $adminData['admin_name'],
                'password'  => $adminData['admin_password'],
                'is_active' => true,
                'email_verified_at' => Carbon::now(),
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
        Log::emergency('🔥 SEND TENANT EMAILS EJECUTADO 🔥');
        // 1. REFRESCAR EL MODELO: Crucial para leer el email_verified_at actualizado
        $this->tenant->refresh();

        Log::info("📧 Verificando envío de mail. Verificado: " . ($this->tenant->email_verified_at ? 'SI' : 'NO'));

        // 2. Flujo DIFERIDO: El usuario ya verificó su mail y la DB se acaba de crear.
        if ($this->tenant->email_verified_at !== null && !config('custom.create_tenant_on_registration')) {
            // Usamos SEND en lugar de QUEUE porque ya estamos dentro de un proceso en segundo plano
            Mail::to($this->tenant->email)->send(new TenantDatabaseReadyMail($this->tenant));
            Log::info("📧 Enviado DIRECTO: TenantDatabaseReadyMail (Clínica lista para usar)");
            return;
        }

        // 3. Flujo ON-THE-FLY: Se acaba de registrar, la DB ya está lista, ahora debe verificar su mail.
        if (config('custom.create_tenant_on_registration') && $this->tenant->email_verified_at === null) {
            Mail::to($this->tenant->email)->send(new VerifyTenantMail($this->tenant));
            Log::info("📧 Enviado DIRECTO: VerifyTenantMail (Esperando verificación del Dr.)");
        }
    }

    protected function handleFailure(Throwable $e): void
    {
        Log::error("❌ ERROR CRÍTICO en Tenant {$this->tenant->id}: {$e->getMessage()}");
        $this->tenant->update(['status' => 'error']);
    }
}
