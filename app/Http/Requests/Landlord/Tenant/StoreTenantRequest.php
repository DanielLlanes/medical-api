<?php

namespace App\Http\Requests\Landlord\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreTenantRequest extends FormRequest
{
    /**
     * Solo los usuarios autorizados (o invitados si es registro abierto)
     */
    public function authorize(): bool
    {
        return true; 
    }

    /**
     * Reglas de validación para el registro "Slim"
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:150'],
            'email' => ['required', 'email', 'max:255', 'unique:landlord.users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            
            // Validamos que el subdominio que se generará no esté ocupado
            // Esto lo haremos manual en el controller o aquí mismo:
            'domain' => ['nullable', 'alpha_dash', 'unique:landlord.tenants,subdomain'],
        ];
    }

    /**
     * Mensajes personalizados para que el usuario no se pierda
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la clínica es obligatorio.',
            'email.unique' => 'Este correo ya está registrado en nuestro sistema.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }
}