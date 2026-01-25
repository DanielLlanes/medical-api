<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasReferenceCodeTrait as HasReferenceCode;

class Subscription extends Model
{
    use HasFactory, SoftDeletes, HasReferenceCode, UsesLandlordConnection;

    const CODE_PREFIX = 'SUB';
    protected $fillable = [
        'tenant_id',
        'plan_id',     // ⬅️ CRÍTICO: Debe estar aquí
        'gateway',
        'status',
        'trial_ends_at',
        'is_active',
        'billing_period',
        'code'         // El Trait lo usará, pero es bueno tenerlo en fillable
    ];

    /**
     * Relación Inversa (1:1)
     * Esta suscripción le pertenece a un Tenant.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

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
