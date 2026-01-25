<?php

namespace App\Console\Commands\Tools; // <-- Esta es la línea que faltaba

use App\Helpers\TenantNamingHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetAllBdCommand extends Command
{
    protected $signature = 'db:clear';

    protected $description = 'Borra bases de datos con prefijo y reinicia Landlord (Solo Desarrollo)';

    public function handle()
    {
        // 🛡️ PROTECCIÓN DE SEGURIDAD
        if (app()->environment('production')) {
            $this->error('¡ERROR: Este comando NO puede ejecutarse en producción!');
            return 1;
        }

        $prefix = TenantNamingHelper::getDbPrefix();

        $this->warn("⚠️  ESTÁS A PUNTO DE BORRAR TODAS LAS DB QUE EMPIECEN CON: {$prefix}");

        if ($this->confirm("¿Realmente quieres continuar? Se perderán todos los datos de los tenants.")) {

            // 1. Obtener bases de datos usando la conexión landlord
            $databases = DB::connection('landlord')->select('SHOW DATABASES');

            foreach ($databases as $db) {
                $name = $db->Database; // En MySQL la columna se llama 'Database'
                if (str_starts_with($name, $prefix)) {
                    DB::connection('landlord')->statement("DROP DATABASE `{$name}`");
                    $this->info("🗑️  Eliminada: {$name}");
                }
            }

            // 2. Limpiar conexiones para evitar conflictos de "Base de datos no encontrada"
            DB::purge('landlord');
            DB::purge('tenant');

            // 3. Fresh del Landlord (Migraciones y Seeders)
            $this->info('🚀 Reiniciando base de datos Landlord...');
            $this->call('migrate:fresh', [
                '--database' => 'landlord',
                '--path'     => 'database/migrations/landlord',
                '--seed'     => true,
                '--force'    => true,
            ]);

            $this->info('✅ ¡Sistema reseteado y listo para nuevas pruebas!');
        }
    }
}
