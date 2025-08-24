<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insertar los estados existentes
        DB::table('statuses')->insert([
            [
                'name' => 'en_ejecucion',
                'label' => 'En Ejecución',
                'color' => '#17a2b8',
                'icon' => 'fas fa-play-circle',
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'culminada',
                'label' => 'Culminada',
                'color' => '#28a745',
                'icon' => 'fas fa-check-circle',
                'order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'en_espera_de_insumos',
                'label' => 'En Espera de Insumos',
                'color' => '#ffc107',
                'icon' => 'fas fa-pause-circle',
                'order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Agregar algunos estados adicionales como ejemplo
            [
                'name' => 'pausada',
                'label' => 'Pausada',
                'color' => '#6c757d',
                'icon' => 'fas fa-pause',
                'order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'cancelada',
                'label' => 'Cancelada',
                'color' => '#dc3545',
                'icon' => 'fas fa-times-circle',
                'order' => 5,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'en_revision',
                'label' => 'En Revisión',
                'color' => '#fd7e14',
                'icon' => 'fas fa-eye',
                'order' => 6,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('statuses')->whereIn('name', [
            'en_ejecucion',
            'culminada', 
            'en_espera_de_insumos',
            'pausada',
            'cancelada',
            'en_revision'
        ])->delete();
    }
};
