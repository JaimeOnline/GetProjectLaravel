<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AlterStatusEnumInActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Modificar el ENUM para agregar los nuevos valores
        DB::statement("ALTER TABLE activities MODIFY COLUMN `status` ENUM(
            'no_iniciada',
            'en_ejecucion',
            'culminada',
            'en_espera_de_insumos',
            'en_certificacion_por_cliente',
            'pases_enviados',
            'pausada',
            'cancelada',
            'reiterar',
            'atendiendo_hoy'
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_ejecucion'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Volver al ENUM anterior (sin los nuevos valores)
        DB::statement("ALTER TABLE activities MODIFY COLUMN `status` ENUM(
            'no_iniciada',
            'en_ejecucion',
            'culminada',
            'en_espera_de_insumos',
            'en_certificacion_por_cliente',
            'pases_enviados',
            'pausada',
            'cancelada'
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_ejecucion'");
    }
}
