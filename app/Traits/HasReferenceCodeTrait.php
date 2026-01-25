<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasReferenceCodeTrait
{
    protected static function bootHasReferenceCodeTrait()
    {
        static::creating(function ($model) {
            if (empty($model->code)) {
                // 1. Intentar constante definida en el modelo
                if (defined('static::CODE_PREFIX')) {
                    $prefix = static::CODE_PREFIX;
                } 
                // 2. Si no, intentar método en el modelo
                elseif (method_exists($model, 'getCodePrefix')) {
                    $prefix = $model->getCodePrefix();
                }
                // 3. Por defecto
                else {
                    $prefix = 'REF';
                }
                
                $model->code = static::generateReferenceCode($prefix);
            }
        });
    }

    public static function generateReferenceCode(string $prefix): string
    {
        $date = now()->format('Ymd');
        
        // Intentamos generar un código único
        do {
            $random = strtoupper(Str::random(4));
            $code = "{$prefix}-{$date}-{$random}";
        } while (static::where('code', $code)->exists());

        return $code;
    }
}