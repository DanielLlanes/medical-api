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
        'name', 'slug', 'price', 'is_active', 'code'
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
}