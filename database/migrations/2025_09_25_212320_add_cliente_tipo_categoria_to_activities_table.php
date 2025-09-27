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
        Schema::table('activities', function (Blueprint $table) {
            $table->unsignedBigInteger('cliente_id')->nullable()->after('orden_analista');
            $table->unsignedBigInteger('tipo_producto_id')->nullable()->after('cliente_id');
            $table->enum('categoria', ['proyecto', 'incidencia', 'mejora_continua'])->default('proyecto')->after('tipo_producto_id');

            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('set null');
            $table->foreign('tipo_producto_id')->references('id')->on('tipos_productos')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
            $table->dropForeign(['tipo_producto_id']);
            $table->dropColumn(['cliente_id', 'tipo_producto_id', 'categoria']);
        });
    }
};
