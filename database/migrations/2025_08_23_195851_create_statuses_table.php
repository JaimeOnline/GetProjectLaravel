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
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Nombre del estado (ej: 'en_ejecucion')
            $table->string('label'); // Etiqueta legible (ej: 'En Ejecución')
            $table->string('color', 7)->default('#007bff'); // Color hexadecimal para el badge
            $table->string('icon')->nullable(); // Icono de FontAwesome
            $table->integer('order')->default(0); // Orden para mostrar en selects
            $table->boolean('is_active')->default(true); // Si el estado está activo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statuses');
    }
};
