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
        // Renombrar la tabla pivot
        Schema::rename('activity_user', 'activity_analista');
        
        // Renombrar la columna user_id a analista_id
        Schema::table('activity_analista', function (Blueprint $table) {
            $table->renameColumn('user_id', 'analista_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir los cambios
        Schema::table('activity_analista', function (Blueprint $table) {
            $table->renameColumn('analista_id', 'user_id');
        });
        
        Schema::rename('activity_analista', 'activity_user');
    }
};
