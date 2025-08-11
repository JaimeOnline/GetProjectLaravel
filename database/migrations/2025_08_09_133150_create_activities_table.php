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
        Schema::create('activities', function (Blueprint $table) {
             $table->id();
        $table->string('name');
        $table->text('description')->nullable();
        $table->enum('status', ['en_ejecucion', 'culminada', 'en_espera_de_insumos'])->default('en_ejecucion');
        $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        $table->foreignId('parent_id')->nullable()->constrained('activities')->nullOnDelete();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
