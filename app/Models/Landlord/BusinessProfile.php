<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasReferenceCodeTrait as HasReferenceCode;

class BusinessProfile extends Model
{
    use HasFactory, SoftDeletes, HasReferenceCode, UsesLandlordConnection;
    
    const CODE_PREFIX = 'BUP';

    protected $fillable = [
        'tenant_id',
        'tax_id',
        'legal_name',
        'tax_regime_id',
        'tax_zip_code',
        'specialty',
        'entity_type',
        'phone_business',
        'website',
        'is_active',
        'code'
    ];
    
    /**
     * Los atributos que deben ser convertidos a fechas.
     * En Laravel moderno se prefiere usar $casts.
     * * @var array<string, string>
     */
    protected $casts = [
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Indica si el modelo debe tener timestamps.
     * * @var bool
     */
    public $timestamps = true;
}