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
    protected $description = 'Crea la suite completa de un módulo (Modelo, CRUD API, Migración, Requests, Policy, Factory y Seeder) para Landlord o Tenant';

    public function handle()
    {
        $name = $this->argument('name');
        $options = $this->options();

        // Si escribes --all o NO escribes ninguna bandera, se hace TODO
        $all = $options['all'] || !($options['model'] || $options['migration'] || $options['controller'] || $options['request'] || $options['policy'] || $options['factory'] || $options['seeder']);

        // Pregunta interactiva (esto se mantiene siempre porque define carpetas)
        $choice = $this->choice("¿Contexto para '{$name}'?", ['Landlord', 'Tenant'], 1);
        $isTenant = ($choice === 'Tenant');
        $context = $isTenant ? 'Tenant' : 'LandLord';

        $this->info("🛠️  Procesando componentes para: {$name}");

        // --- EJECUCIÓN SELECTIVA ---

        // 1. Modelo (Se crea si pides --all, --model, o si pides piezas que dependen de él como el Controller)
        if ($all || $options['model'] || $options['controller']) {
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

        $this->info("✅ Proceso de '{$name}' finalizado con éxito.");
    }

    /**
     * Lógica para el Modelo (Usa Stub Personalizado)
     */
    protected function createModel($name, $context, $isTenant)
    {
        $stub = File::get(base_path('stubs/model.multitenant.stub'));
        
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

    /**
     * Lógica para el Controlador (Usa Stub Personalizado)
     */
    protected function createController($name, $context)
    {
        $stub = File::get(base_path('stubs/controller.multitenant.stub'));
        
        $baseController = "Base{$context}Controller";

        $content = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ baseController }}', '{{ rootNamespace }}'],
            ["App\\Http\\Controllers\\{$context}", "{$name}Controller", $baseController, 'App\\'],
            $stub
        );

        $path = app_path("Http/Controllers/{$context}/{$name}Controller.php");
        $this->saveFile($path, $content);
    }

    /**
     * Lógica para los Form Requests (Usa Stub Personalizado)
     */
    protected function createRequest($name, $context)
    {
        $stub = File::get(base_path('stubs/request.multitenant.stub'));

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

    /**
     * Lógica para la Migración (Usa Stub Personalizado)
     */
    protected function createMigration($name, $isTenant)
    {
        $stub = File::get(base_path('stubs/migration.multitenant.stub'));
        
        $tableName = Str::snake(Str::pluralStudly($name));
        $fileName = date('Y_m_d_His') . "_create_{$tableName}_table.php";

        $content = str_replace(['{{ table }}'], [$tableName], $stub);

        $folder = $isTenant ? 'tenant' : 'landlord';
        $path = database_path("migrations/{$folder}/{$fileName}");

        $this->saveFile($path, $content);
    }

    /**
     * Lógica para la Policy
     */
    protected function createPolicy($name, $context)
    {
        $this->call('make:policy', [
            'name' => "{$context}/{$name}Policy",
            '--model' => "App\\Models\\{$context}\\{$name}"
        ]);
    }

    /**
     * Lógica para el Factory
     */
    protected function createFactory($name, $context)
    {
        $this->call('make:factory', [
            'name' => "{$context}/{$name}Factory",
            '--model' => "App\\Models\\{$context}\\{$name}"
        ]);
    }

    /**
     * Lógica para el Seeder
     */
    protected function createSeeder($name, $context)
    {
        $this->call('make:seeder', [
            'name' => "{$context}/{$name}Seeder"
        ]);
    }

    /**
     * Auxiliar para guardar archivos y crear carpetas
     */
    protected function saveFile($path, $content)
    {
        $directory = dirname($path);
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        if (File::exists($path)) {
            $this->error("Archivo ya existe: {$path}");
            return;
        }

        File::put($path, $content);
        $this->line("<info>Creado:</info> {$path}");
    }
}