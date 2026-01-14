<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Traits\HasReferenceCodeTrait as HasReferenceCode;
use Spatie\Multitenancy\Models\Tenant as SpatieTenant;

class Tenant extends SpatieTenant
{
    use HasFactory, SoftDeletes, HasReferenceCode, UsesLandlordConnection;

    const CODE_PREFIX = 'TEN';

    protected $fillable = [
        'name', 
        'domain', 
        'custom_domain', 
        'database', 
        'plan_id', 
        'status',
        'is_active', // Falta este
        'code'       // Falta este
    ];

    /**
     * El Tenant pertenece a un plan específico.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * El Tenant tiene un perfil fiscal y estadístico.
     */
    public function businessProfile(): HasOne
    {
        return $this->hasOne(BusinessProfile::class);
    }

    /**
     * El Tenant tiene una suscripción (Pagos).
     */
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    // Ya no necesitas $guarded si ya declaraste $fillable. 
    // Laravel prioriza fillable.

    // El atributo $dates está obsoleto en Laravel 10+, 
    // se recomienda usar $casts si necesitas fechas especiales.
    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

}