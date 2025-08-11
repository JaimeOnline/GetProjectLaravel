<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateRequirementsTable extends Migration
{
    public function up()
    {
        Schema::create('requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained()->onDelete('cascade'); // Llave foránea a la tabla activities
            $table->text('description'); // Descripción del requerimiento
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('requirements');
    }
}