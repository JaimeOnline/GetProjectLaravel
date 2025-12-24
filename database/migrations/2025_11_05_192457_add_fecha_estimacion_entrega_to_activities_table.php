<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->date('fecha_estimacion_entrega')->nullable()->after('porcentaje_avance');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('fecha_estimacion_entrega');
        });
    }
};
