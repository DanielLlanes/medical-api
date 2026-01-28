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
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('annual_discount', 10, 2)->default(0); // Sugerencia: para el descuento del 25%

            $table->integer('limit_users')->default(1);
            $table->integer('limit_storage_gb')->default(1); // Sugerencia: control de archivos médicos
            $table->integer('trial_days')->default(14); // Sugerencia: para los 14 días gratis

            $table->boolean('has_custom_domain')->default(false);
            $table->json('features')->nullable();
            $table->boolean('is_recommended')->default(false);

            $table->enum('status', ['pending', 'active', 'suspended'])->default('active'); // Cambiado a active por defecto
            $table->boolean('is_active')->default(true);
            $table->string('code')->unique();

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
