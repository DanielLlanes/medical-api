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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            
            // Gateway Info
            $table->string('gateway')->default('mercadopago'); 
            $table->string('gateway_customer_id')->nullable(); 
            $table->string('gateway_subscription_id')->nullable();
            
            // Status (Agregado para que coincida con tu lógica de negocio)
            $table->string('status')->default('trialing'); // trialing, active, past_due, canceled

            // Dates
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('next_billing_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            // Control Fields (Standardized)
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
        Schema::dropIfExists('subscriptions');
    }
};