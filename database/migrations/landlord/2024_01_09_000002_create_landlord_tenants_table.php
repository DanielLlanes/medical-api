<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tenants', function (Blueprint $table){
            $table->id();
            $table->string('name'); // Nombre del responsable
            $table->string('email'); // Email del admin del tenant
            $table->string('company'); // "Colon and Mccormick Plc" que llega en tu Request

            // Acceso técnico y Multi-tenancy
            $table->string('domain')->unique();
            $table->string('custom_domain')->unique()->nullable();
            $table->string('database')->unique();

            // Configuración del Expediente y Perfil
            // Guardamos la especialidad aquí para que el Job sepa qué perfil crear en el Tenant
            $table->unsignedBigInteger('specialty_id')->nullable();
            $table->boolean('diffusion_enabled')->default(false); // Autorización de difusión

            $table->json('setup_data')->nullable(); // Para settings extra

            // Estado y Suscripción
            // Asumimos que la tabla 'plans' ya existe
            $table->string('plan_id'); // En tu request llega como string: "plan_consultorio_01"
            $table->enum('status', ['pending', 'trialing', 'active', 'past_due', 'suspended', 'banned', 'error'])->default('pending');
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('is_active')->default(false);
            $table->string('code')->unique(); 

            $table->timestamps();
            $table->softDeletes();
        });
    }
};
