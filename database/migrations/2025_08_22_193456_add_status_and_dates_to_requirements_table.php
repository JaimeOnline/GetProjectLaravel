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
        Schema::table('requirements', function (Blueprint $table) {
            $table->enum('status', ['pendiente', 'recibido'])->default('pendiente')->after('description');
            $table->timestamp('fecha_recepcion')->nullable()->after('status');
            $table->text('notas')->nullable()->after('fecha_recepcion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requirements', function (Blueprint $table) {
            $table->dropColumn(['status', 'fecha_recepcion', 'notas']);
        });
    }
};
