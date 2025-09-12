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
        Schema::table('activities', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->integer('prioridad')->default(1);
            $table->integer('orden_analista')->default(1);
        });
    }

    public function down()
    {
        Schema::table('activities', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->dropColumn(['prioridad', 'orden_analista']);
        });
    }
};
