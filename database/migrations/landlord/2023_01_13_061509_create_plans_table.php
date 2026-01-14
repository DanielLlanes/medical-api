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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Free, Pro, Premium, Beta Tester
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('limit_users')->default(1);
            $table->boolean('has_custom_domain')->default(false); // Servicio Premium
            $table->enum('status', ['pending', 'active', 'suspended'])->default('pending');
            // Campos de control (estandarizados)
            $table->boolean('is_active')->default(true); 
            $table->string('code')->unique(); // Ej: PLAN-FREE, PLAN-PRO
            
            $table->timestamps();
            $table->softDeletes();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};