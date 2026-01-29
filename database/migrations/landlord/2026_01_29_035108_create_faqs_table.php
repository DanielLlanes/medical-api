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
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();

            // Campo de referencia única que mencionaste
            $table->string('code')->unique()->comment('Código de referencia generado por el sistema');

            $table->string('question');
            $table->text('answer');

            // Categoría (Venta, Operativa, etc.)
            $table->string('category')->index();

            // Orden y Estado
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);


            $table->timestamps();
            $table->softDeletes(); // Recomendado para no perder historial médico/soporte

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
