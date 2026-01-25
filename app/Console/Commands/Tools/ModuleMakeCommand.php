<?php

namespace App\Console\Commands\Tools;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleMakeCommand extends Command
{
    protected $signature = 'make:module {name}
        {--model}
        {--m|migration}
        {--c|controller}
        {--r|request}
        {--p|policy}
        {--f|factory}
        {--s|seeder}
        {--res|resource}
        {--all}';

    protected $description = 'Crea un módulo Landlord o Tenant con inyección automática de Traits y Requests';

    public function handle()
    {
        $raw = $this->argument('name');
        $name = Str::studly(basename(str_replace(['\\', '/'], '/', $raw)));

        $options = $this->options();
        $any = $options['model'] || $options['migration'] || $options['controller'] ||
               $options['request'] || $options['policy'] || $options['factory'] || 
               $options['seeder'] || $options['resource'];

        $all = $options['all'] || !$any;

        $choice = $this->choice("Contexto del módulo '{$name}'", ['Landlord', 'Tenant'], 1);
        $context = Str::studly(strtolower($choice)); 
        $isTenant = $context === 'Tenant';

        $this->info("🧩 Generando arquitectura para: {$name} en {$context}");

        // El orden importa: Primero Requests para que el Controller pueda referenciarlos
        if ($all || $options['request'])    $this->createRequests($name, $context);
        if ($all || $options['model'])      $this->createModel($name, $context, $isTenant);
        if ($all || $options['controller']) $this->createController($name, $context);
        if ($all || $options['migration'])  $this->createMigration($name, $isTenant);
        if ($all || $options['policy'])     $this->createPolicy($name, $context);
        if ($all || $options['factory'])    $this->createFactory($name, $context);
        if ($all || $options['seeder'])     $this->createSeeder($name, $context);
        if ($all || $options['resource'])   $this->createResource($name, $context);

        $this->info("✅ Módulo {$name} generado exitosamente.");
    }

    protected function createModel($name, $context, $isTenant)
    {
        $stub = base_path('stubs/model.multitenant.stub');
        if (!File::exists($stub)) return $this->error('Falta stubs/model.multitenant.stub');

        $traitImport = $isTenant
            ? 'use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;'
            : 'use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;';

        $traitName = $isTenant ? 'UsesTenantConnection' : 'UsesLandlordConnection';
        
        // Generar prefijo automático (Ej: User -> U)
        $prefix = strtoupper(substr($name, 0, 1));

        $content = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ traitImport }}', '{{ traitName }}', '{{ prefix }}'],
            ["App\\Models\\{$context}", $name, $traitImport, $traitName, $prefix],
            File::get($stub)
        );

        $this->save(app_path("Models/{$context}/{$name}.php"), $content);
    }

    protected function createController($name, $context)
    {
        $stub = base_path('stubs/controller.multitenant.stub');
        if (!File::exists($stub)) return $this->error('Falta stubs/controller.multitenant.stub');

        $storeRequestClass = "Store{$name}Request";
        $updateRequestClass = "Update{$name}Request";

        $content = str_replace(
            [
                '{{ namespace }}', 
                '{{ class }}', 
                '{{ baseController }}', 
                '{{ context }}',
                '{{ model }}',
                '{{ storeRequestClass }}',
                '{{ updateRequestClass }}',
                '{{ storeRequestPath }}',
                '{{ updateRequestPath }}'
            ],
            [
                "App\\Http\\Controllers\\{$context}", 
                "{$name}Controller", 
                "Base{$context}Controller",
                $context,
                $name,
                $storeRequestClass,
                $updateRequestClass,
                "App\\Http\\Requests\\{$context}\\{$name}\\{$storeRequestClass}",
                "App\\Http\\Requests\\{$context}\\{$name}\\{$updateRequestClass}"
            ],
            File::get($stub)
        );

        $this->save(app_path("Http/Controllers/{$context}/{$name}Controller.php"), $content);
    }

    protected function createRequests($name, $context)
    {
        $stub = base_path('stubs/request.multitenant.stub');
        if (!File::exists($stub)) return $this->error('Falta stubs/request.multitenant.stub');

        foreach (['Store', 'Update'] as $type) {
            $class = "{$type}{$name}Request";
            $content = str_replace(
                ['{{ namespace }}', '{{ class }}'],
                ["App\\Http\\Requests\\{$context}\\{$name}", $class],
                File::get($stub)
            );

            $this->save(app_path("Http/Requests/{$context}/{$name}/{$class}.php"), $content);
        }
    }

    protected function createMigration($name, $isTenant)
    {
        $stub = base_path('stubs/migration.multitenant.stub');
        if (!File::exists($stub)) return $this->error('Falta stubs/migration.multitenant.stub');

        $table = Str::snake(Str::pluralStudly($name));
        $file = date('Y_m_d_His') . "_create_{$table}_table.php";
        $content = str_replace('{{ table }}', $table, File::get($stub));
        $folder = $isTenant ? 'tenant' : 'landlord';

        $this->save(database_path("migrations/{$folder}/{$file}"), $content);
    }

    protected function createResource($name, $context)
    {
        $stubPath = base_path('stubs/resource.multitenant.stub');
        if (!File::exists($stubPath)) return $this->warn('No existe el stub de resource.');

        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            ["App\\Http\\Resources\\{$context}", "{$name}Resource"],
            File::get($stubPath)
        );

        $this->save(app_path("Http/Resources/{$context}/{$name}Resource.php"), $content);
    }

    protected function createPolicy($name, $context)
    {
        $this->call('make:policy', [
            'name' => "{$context}/{$name}Policy", 
            '--model' => "App\\Models\\{$context}\\{$name}"
        ]);
    }

    protected function createFactory($name, $context)
    {
        $this->call('make:factory', [
            'name' => "{$context}/{$name}Factory", 
            '--model' => "App\\Models\\{$context}\\{$name}"
        ]);
    }

    protected function createSeeder($name, $context)
    {
        $this->call('make:seeder', ['name' => "{$context}/{$name}Seeder"]);
    }

    protected function save($path, $content)
    {
        if (!File::isDirectory(dirname($path))) File::makeDirectory(dirname($path), 0755, true);
        if (File::exists($path)) return $this->warn("⏭ {$path} ya existe");

        File::put($path, $content);
        $this->line("✔ {$path}");
    }
}