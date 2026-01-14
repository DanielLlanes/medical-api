<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class TenantNamingHelper
{
    /**
     * Prefijo estable del sistema (DBs_medical_api)
     */
    public static function getDbPrefix(): string
    {
        $main = config('custom.db_main_prefix', 'DBs');
        $app  = config('custom.app_name_slug', 'medicalapi');

        $cleanApp = Str::of($app)
            ->lower()
            ->replace([' ', '-'], '')
            ->replaceMatches('/[^a-z0-9_]/', '')
            ->replaceMatches('/_+/', '_')
            ->trim('_');

        return "{$main}_{$cleanApp}";
    }

    /**
     * Sufijo corto único
     */
    public static function getDbSuffix(): string
    {
        return Str::lower(Str::random(6));
    }

    /**
     * Nombre final de base de datos tenant
     */
    public static function generateDatabaseName(string $tenantName): string
    {
        $prefix = self::getDbPrefix();

        $slug = Str::of($tenantName)
            ->lower()
            ->replace([' ', '-'], '_')
            ->replaceMatches('/[^a-z0-9_]/', '')
            ->replaceMatches('/_+/', '_')
            ->trim('_');

        $suffix = self::getDbSuffix();


        return substr("{$prefix}_{$slug}_{$suffix}", 0, 45);
    }

    public static function generateSubdomain(string $name): string {
        $slug = Str::slug($name);
        $baseDomain = config('custom.base_domain'); // medical.test
        
        return "{$slug}.{$baseDomain}";
    }


}