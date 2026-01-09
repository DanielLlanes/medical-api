<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class TenantNamingHelper
{
    /**
     * Devuelve únicamente el prefijo configurado.
     * Útil para el comando db:clear o validaciones.
     */
    public static function getDbPrefix(): string 
    {
        $rawPrefix = Config::get('custom.bd_prefix', 'medical');
        // Limpiamos y formateamos el prefijo: DBs_medical
        return 'DBs_' . Str::replace(['-', '_'], '', Str::slug($rawPrefix));
    }

    /**
     * Genera un sufijo aleatorio.
     */
    public static function getDbSuffix(): string
    {
        return Str::lower(Str::random(6));
    }

    /**
     * Une las piezas para crear el nombre final.
     */
    public static function generateDatabaseName(string $tenantName): string
    {
        $prefix = self::getDbPrefix(); // Llamamos a la función independiente
        $slug = Str::slug($tenantName, '_');
        $suffix = self::getDbSuffix();

        $fullName = "{$prefix}_{$slug}_{$suffix}";

        return substr($fullName, 0, 45);
    }
}