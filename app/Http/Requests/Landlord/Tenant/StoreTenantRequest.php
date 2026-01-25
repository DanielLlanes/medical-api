<?php

namespace App\Http\Requests\Landlord\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            'name'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'email', 'unique:landlord.users,email'],
            'password' => ['required', 'confirmed'],
            'company'  => ['required', 'string', 'max:100'],
            'plan_id'  => ['required', 'string', 'exists:landlord.plans,slug'],
        ];
    }

    /**
     * Mensajes personalizados para que el usuario no se pierda
     */
    public function messages(): array
    {
        return [
            // Nombre del médico
            'name.required' => 'Necesitamos saber tu nombre para crear tu cuenta.',
            'name.min' => 'El nombre es muy corto, dinos quién eres.',

            // Correo electrónico
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Esa dirección de correo no parece válida.',
            'email.unique' => 'Este correo ya está registrado. ¿Ya tienes una cuenta?',

            // Contraseña
            'password.required' => 'Debes definir una contraseña para proteger tu acceso.',
            'password.confirmed' => 'Las contraseñas no coinciden, verifícalas.',
            'password.min' => 'Por seguridad, usa al menos 8 caracteres.',

            // Clínica / Empresa
            'company.required' => 'Dinos el nombre de tu clínica o consultorio.',
            'company.max' => 'El nombre de la clínica es demasiado largo.',

            // Plan (El slug que validamos contra la DB)
            'plan_id.required' => 'No has seleccionado ningún plan de suscripción.',
            'plan_id.exists' => 'El plan seleccionado no es válido o ya no está disponible.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        // 1. Mandamos los errores al log de la API (storage/logs/laravel.log)
        \Log::error('❌ Errores de validación en Registro:', [
            'datos_enviados' => $this->all(),
            'errores' => $validator->errors()->toArray()
        ]);

        // 2. Lanzamos la respuesta para que la Landing sepa qué pasó
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422));
    }
}
