<?php

namespace App\Models\LandLord;

use Spatie\Multitenancy\Models\Tenant as SpatieTenant;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantNamingHelper;
use Illuminate\Support\Str;

class Tenant extends SpatieTenant
{
    protected $fillable = ['name', 'domain', 'database'];

    public static function booted()
    {
        // Exactamente como tu captura
        static::creating(fn (Tenant $tenant) => $tenant->createDatabase($tenant));
        static::created(fn (Tenant $tenant) => $tenant->runMigrationsSeeders($tenant));
    }

    public function createDatabase($tenant)
    {
        // Usamos tu Helper para el nombre
        $database = TenantNamingHelper::generateDatabaseName($tenant->name);
        
        // Lógica de validación de tu captura
        $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?";
        $db = DB::select($query, [$database]);

        if (empty($db)) {
            // TAL CUAL TU CAPTURA: Usando la conexión 'tenant'
            DB::connection('tenant')->statement("CREATE DATABASE {$database};");
            $tenant->database = $database;
        }

        // Generamos el dominio para que no quede vacío
        if (!$tenant->domain) {
            $tenant->domain = Str::slug($tenant->name) . '.medical.test';
        }

        return $database;
    }

    public function runMigrationsSeeders($tenant)
    {
        // Exactamente como tu captura de pantalla
        $tenant->refresh();

        Artisan::call('tenants:artisan', [
            'artisanCommand' => "migrate --database=tenant --seed --force",
            '--tenant' => "{$tenant->id}",
        ]);
    }
}