<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddRecepcionDateToActivitiesTable extends Migration
{
    public function up()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->date('fecha_recepcion')->nullable(); // Agrega la columna de fecha de recepción
        });
    }
    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('fecha_recepcion'); // Elimina la columna si es necesario
        });
    }
}
