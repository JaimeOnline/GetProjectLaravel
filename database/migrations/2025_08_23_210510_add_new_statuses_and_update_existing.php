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
        // 1. Actualizar el estado "En Revisión" a "En Certificación por Cliente"
        DB::table('statuses')
            ->where('name', 'en_revision')
            ->update([
                'name' => 'en_certificacion_por_cliente',
                'label' => 'En Certificación por Cliente',
                'color' => '#fd7e14', // Mantener el color naranja
                'icon' => 'fas fa-certificate',
                'updated_at' => now(),
            ]);

        // 2. Agregar los nuevos estados
        DB::table('statuses')->insert([
            [
                'name' => 'no_iniciada',
                'label' => 'No Iniciada',
                'color' => '#6c757d',
                'icon' => 'fas fa-clock',
                'order' => 0, // Orden 0 para que aparezca primero
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'pases_enviados',
                'label' => 'Pases Enviados',
                'color' => '#20c997',
                'icon' => 'fas fa-paper-plane',
                'order' => 7, // Después de los estados existentes
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 3. Reordenar todos los estados para que tengan un orden lógico
        $statusOrder = [
            'no_iniciada' => 1,
            'en_ejecucion' => 2,
            'en_espera_de_insumos' => 3,
            'pausada' => 4,
            'en_certificacion_por_cliente' => 5,
            'pases_enviados' => 6,
            'culminada' => 7,
            'cancelada' => 8,
        ];

        foreach ($statusOrder as $statusName => $order) {
            DB::table('statuses')
                ->where('name', $statusName)
                ->update(['order' => $order, 'updated_at' => now()]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir el cambio de "En Certificación por Cliente" a "En Revisión"
        DB::table('statuses')
            ->where('name', 'en_certificacion_por_cliente')
            ->update([
                'name' => 'en_revision',
                'label' => 'En Revisión',
                'icon' => 'fas fa-eye',
                'updated_at' => now(),
            ]);

        // Eliminar los nuevos estados
        DB::table('statuses')->whereIn('name', [
            'no_iniciada',
            'pases_enviados'
        ])->delete();

        // Restaurar el orden original
        $originalOrder = [
            'en_ejecucion' => 1,
            'culminada' => 2,
            'en_espera_de_insumos' => 3,
            'pausada' => 4,
            'cancelada' => 5,
            'en_revision' => 6,
        ];

        foreach ($originalOrder as $statusName => $order) {
            DB::table('statuses')
                ->where('name', $statusName)
                ->update(['order' => $order, 'updated_at' => now()]);
        }
    }
};
