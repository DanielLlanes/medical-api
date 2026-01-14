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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre comercial
            
            // Acceso técnico
            $table->string('domain')->unique(); // el-perez
            $table->string('custom_domain')->unique()->nullable(); // clinica-perez.com (PREMIUM)
            $table->string('database')->unique();
            
            // Estado y Relación
            $table->foreignId('plan_id')->constrained(); 
            $table->enum('status', ['trialing', 'active', 'past_due', 'suspended'])->default('trialing');
            $table->boolean('is_active')->default(true);
            $table->string('code')->unique();
            $table->timestamps();
            $table->softDeletes(); 
        });
    }
};
