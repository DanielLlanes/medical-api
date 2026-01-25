<?php

namespace App\Console\Commands\Tools;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CustomMakeCommand extends Command
{
    protected $signature = 'make:custom {name}';
    protected $description = 'Crea una nueva clase personalizada (Service, Trait, Helper, etc.)';

    public function handle()
    {
        $opciones = [
            '1' => 'Service: Lógica de negocio específica.',
            '2' => 'Trait: Reutilización de código.',
            '3' => 'Helper: Funciones auxiliares.',
            '4' => 'Class: Clase general personalizada.',
            '5' => 'Util: Utilidades del sistema.',
        ];

        $this->info('Seleccione el tipo de archivo que desea crear:');
        foreach ($opciones as $numero => $descripcion) {
            $this->info("[$numero] $descripcion");
        }

        $num = $this->ask('Ingrese el número');

        if (!array_key_exists($num, $opciones)) {
            $this->error('Número no válido.');
            return;
        }

        // Determinamos el tipo y la palabra clave (keyword)
        $tipoSeleccionado = match ($num) {
            '1' => 'Service',
            '2' => 'Trait',
            '3' => 'Helper',
            '4' => 'Class',
            '5' => 'Util',
        };

        $keyword = ($tipoSeleccionado === 'Trait') ? 'trait' : 'class';
        
        // Formateo de nombres
        $inputName = $this->argument('name');
        
        // Si el usuario no escribió el sufijo (ej: ApiResponse), se lo ponemos (ApiResponseTrait)
        // Pero si ya lo escribió, no lo duplicamos.
        $className = Str::studly($inputName);
        if (!Str::endsWith($className, $tipoSeleccionado)) {
            $className .= $tipoSeleccionado;
        }

        $tipoPlural = Str::plural($tipoSeleccionado);
        $namespace = "App\\{$tipoPlural}";
        $directoryPath = app_path($tipoPlural);
        $filePath = "{$directoryPath}/{$className}.php";

        // Verificar existencia
        if (File::exists($filePath)) {
            $this->error("¡El {$tipoSeleccionado} {$className} ya existe!");
            return;
        }

        // Crear directorio si no existe
        if (!File::isDirectory($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }

        // Procesar Stub
        $stubPath = base_path('stubs/custom.stub');
        if (!File::exists($stubPath)) {
            $this->error("No se encontró el stub en {$stubPath}");
            return;
        }

        $content = File::get($stubPath);
        $content = str_replace(
            ['{{ namespace }}', '{{ keyword }}', '{{ class }}'],
            [$namespace, $keyword, $className],
            $content
        );

        File::put($filePath, $content);

        $this->info("✅ {$tipoSeleccionado} creado con éxito en: {$filePath}");
    }
}