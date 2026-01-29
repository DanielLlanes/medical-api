<?php

namespace App\Models\Tenant;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use App\Traits\HasReferenceCodeTrait as HasReferenceCode;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasReferenceCode, UsesTenantConnection; 
    const CODE_PREFIX = 'USR';   
    protected $fillable = [ 'name', 'email', 'password', 'is_active', 'email_verified_at', 'code', ];    
    protected $hidden = [ 'password', 'remember_token', ];    
    protected $casts = ['email_verified_at' => 'datetime', 'password' => 'hashed', 'is_active' => 'boolean',]; 
 }
