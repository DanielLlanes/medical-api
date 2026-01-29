<?php

namespace App\Models\Landlord;

use App\Traits\HasReferenceCodeTrait as HasReferenceCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;


class Faq extends Model
{
    /** @use HasFactory<\Database\Factories\FaqsFactory> */
    use HasFactory, SoftDeletes, HasReferenceCode, UsesLandlordConnection;

    const CODE_PREFIX = 'PLN';

    protected $fillable = ['code', 'question', 'answer', 'category', 'order', 'is_active',];

    protected $casts = ['is_active' => 'boolean', 'order'     => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime',];

    // 1. Solo filtra el estado
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // 2. Solo se encarga de la posición
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    // 3. Solo filtra la categoría
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
