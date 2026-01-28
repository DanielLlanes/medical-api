<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasReferenceCodeTrait
{
    protected static function bootHasReferenceCodeTrait()
    {
        static::creating(function ($model) {
            if (empty($model->code)) {

                if (defined('static::CODE_PREFIX')) {
                    $prefix = static::CODE_PREFIX;
                }

                elseif (method_exists($model, 'getCodePrefix')) {
                    $prefix = $model->getCodePrefix();
                }

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


        do {
            $random = strtoupper(Str::random(4));
            $code = "{$prefix}-{$date}-{$random}";
        } while (static::where('code', $code)->exists());

        return $code;
    }
}
