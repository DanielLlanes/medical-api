<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('business_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            
            // Tax Data (SAT México)
            $table->string('tax_id', 13)->nullable(); // RFC
            $table->string('legal_name')->nullable(); // Razón Social
            $table->string('tax_regime_id', 3)->nullable(); // Ej: 601, 612
            $table->string('tax_zip_code', 5)->nullable(); // CP Fiscal
            
            // Stats & Segmenting (Tu "Coco")
            $table->string('specialty')->nullable(); // Dental, Veterinaria, etc.
            $table->enum('entity_type', ['person', 'company'])->nullable(); // fisica, moral
            $table->string('phone_business')->nullable();
            $table->string('website')->nullable();

            // Control Fields
            $table->boolean('is_active')->default(true);
            $table->string('code')->unique(); // Un código interno para el perfil si lo requieres
            
            $table->timestamps();
            $table->softDeletes();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_profiles');
    }
};