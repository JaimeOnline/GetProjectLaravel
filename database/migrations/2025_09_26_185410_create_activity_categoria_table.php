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
        Schema::create('activity_categoria', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_id');
            $table->string('categoria'); // 'proyecto', 'incidencia', 'mejora_continua'
            $table->foreign('activity_id')->references('id')->on('activities')->onDelete('cascade');
            $table->index(['activity_id', 'categoria']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_categoria');
    }
};
