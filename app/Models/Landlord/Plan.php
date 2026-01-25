<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasReferenceCodeTrait as HasReferenceCode;

class Plan extends Model
{
    use HasFactory, SoftDeletes, HasReferenceCode, UsesLandlordConnection;

    protected $fillable = [
        'name', 'slug', 'description', 'price', 'price_annual',
        'limit_users', 'limit_storage_gb', 'trial_days',
        'has_custom_domain', 'features', 'is_recommended',
        'status', 'is_active', 'code'
    ];

    const CODE_PREFIX = 'PLN';

    public function getCodePrefix()
    {
        return 'PLN';
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    protected $casts = [
        'features' => 'array',
        'is_recommended' => 'boolean',
        'is_active' => 'boolean',
        'has_custom_domain' => 'boolean',
        'price' => 'decimal:2',
    ];
}
