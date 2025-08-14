<?php

// database/migrations/xxxx_xx_xx_add_caso_to_activities_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddCasoToActivitiesTable extends Migration
{
    public function up()
{
    Schema::table('activities', function (Blueprint $table) {
        $table->string('caso')->nullable()->after('status'); // Permitir valores nulos
    });
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('caso'); // Elimina la columna 'caso' si se revierte la migración
        });
    }
}
}