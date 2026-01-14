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
        'gateway', 
        'gateway_customer_id', 
        'gateway_subscription_id', 
        'status', 
        'trial_ends_at',
        'next_billing_at',
        'ends_at',
        'is_active',
        'code'
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