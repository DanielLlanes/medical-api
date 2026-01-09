<?php

namespace App\Console\Commands\Tools;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleMakeCommand extends Command
{
    protected $signature = 'make:module {name} 
                            {--model : Crear solo el modelo}
                            {--m|migration : Crear solo la migración}
                            {--c|controller : Crear solo el controlador}
                            {--r|request : Crear solo los requests}
                            {--p|policy : Crear solo la policy}
                            {--f|factory : Crear solo el factory}
                            {--s|seeder : Crear solo el seeder}
                            {--all : Crear la suite completa}';

    protected $description = 'Crea componentes de un módulo de forma selectiva para Landlord o Tenant';

    public function handle()
    {
        // LIMPIEZA DE NOMBRE: Si el usuario mete "LandLord/Tenant", nos quedamos solo con "Tenant"
        $inputName = $this->argument('name');
        $name = Str::studly(basename(str_replace(['\\', '/'], '/', $inputName)));

        $options = $this->options();

        // Determinar si quiere TODO o piezas sueltas
        $anyOption = $options['model'] || $options['migration'] || $options['controller'] || 
                     $options['request'] || $options['policy'] || $options['factory'] || $options['seeder'];
        
        $all = $options['all'] || !$anyOption;

        // Pregunta interactiva para el contexto
        $choice = $this->choice("¿Contexto para el módulo '{$name}'?", ['Landlord', 'Tenant'], 1);
        $isTenant = ($choice === 'Tenant');
        $context = $isTenant ? 'Tenant' : 'LandLord';

        $this->info("🛠️  Procesando componentes para: {$name} en contexto {$context}");

        // 1. Modelo
        if ($all || $options['model']) {
            $this->createModel($name, $context, $isTenant);
        }

        // 2. Controlador
        if ($all || $options['controller']) {
            $this->createController($name, $context);
        }

        // 3. Requests
        if ($all || $options['request']) {
            $this->createRequest($name, $context);
        }

        // 4. Migración
        if ($all || $options['migration']) {
            $this->createMigration($name, $isTenant);
        }

        // 5. Policy, Factory, Seeder
        if ($all || $options['policy'])  $this->createPolicy($name, $context);
        if ($all || $options['factory']) $this->createFactory($name, $context);
        if ($all || $options['seeder'])  $this->createSeeder($name, $context);

        $this->info("✅ Proceso de '{$name}' finalizado.");
    }

    protected function createModel($name, $context, $isTenant)
    {
        $stubPath = base_path('stubs/model.multitenant.stub');
        if (!File::exists($stubPath)) return $this->error("No existe el stub de modelo.");

        $stub = File::get($stubPath);
        
        $traitImport = $isTenant 
            ? 'use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;' 
            : 'use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;';
        
        $traitName = $isTenant ? 'UsesTenantConnection' : 'UsesLandlordConnection';

        $content = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ traitImport }}', '{{ traitName }}'],
            ["App\\Models\\{$context}", $name, $traitImport, $traitName],
            $stub
        );

        $path = app_path("Models/{$context}/{$name}.php");
        $this->saveFile($path, $content);
    }

    protected function createController($name, $context)
    {
        $stubPath = base_path('stubs/controller.multitenant.stub');
        if (!File::exists($stubPath)) return $this->error("No existe el stub de controlador.");

        $stub = File::get($stubPath);
        $baseController = "Base{$context}Controller";

        $content = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ baseController }}', '{{ rootNamespace }}'],
            ["App\\Http\\Controllers\\{$context}", "{$name}Controller", $baseController, 'App\\'],
            $stub
        );

        $path = app_path("Http/Controllers/{$context}/{$name}Controller.php");
        $this->saveFile($path, $content);
    }

    protected function createRequest($name, $context)
    {
        $stubPath = base_path('stubs/request.multitenant.stub');
        if (!File::exists($stubPath)) return $this->error("No existe el stub de request.");
        $stub = File::get($stubPath);

        foreach (['Store', 'Update'] as $type) {
            $className = "{$type}{$name}Request";
            $content = str_replace(
                ['{{ namespace }}', '{{ class }}'],
                ["App\\Http\\Requests\\{$context}\\{$name}", $className],
                $stub
            );

            $path = app_path("Http/Requests/{$context}/{$name}/{$className}.php");
            $this->saveFile($path, $content);
        }
    }

    protected function createMigration($name, $isTenant)
    {
        $stubPath = base_path('stubs/migration.multitenant.stub');
        if (!File::exists($stubPath)) return $this->error("No existe el stub de migración.");
        $stub = File::get($stubPath);
        
        $tableName = Str::snake(Str::pluralStudly($name));
        $fileName = date('Y_m_d_His') . "_create_{$tableName}_table.php";
        $content = str_replace(['{{ table }}'], [$tableName], $stub);

        $folder = $isTenant ? 'tenant' : 'landlord';
        $path = database_path("migrations/{$folder}/{$fileName}");

        $this->saveFile($path, $content);
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
        $this->call('make:seeder', [
            'name' => "{$context}/{$name}Seeder"
        ]);
    }

    protected function saveFile($path, $content)
    {
        $directory = dirname($path);
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        if (File::exists($path)) {
            $this->warn("El archivo ya existe, saltando: {$path}");
            return;
        }

        File::put($path, $content);
        $this->line("<info>Creado:</info> {$path}");
    }
}