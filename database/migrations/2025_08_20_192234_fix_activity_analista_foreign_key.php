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
        Schema::table('activity_analista', function (Blueprint $table) {
            // Eliminar la constraint antigua que apunta a users
            $table->dropForeign('activity_user_user_id_foreign');
            
            // Crear la nueva constraint que apunta a analistas
            $table->foreign('analista_id')->references('id')->on('analistas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_analista', function (Blueprint $table) {
            // Eliminar la constraint nueva
            $table->dropForeign(['analista_id']);
            
            // Restaurar la constraint antigua (aunque no debería usarse)
            $table->foreign('analista_id', 'activity_user_user_id_foreign')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
