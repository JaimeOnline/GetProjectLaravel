<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CleanActivitiesSeeder extends Seeder
{
    public function run()
    {
        // Elimina relaciones pivote primero
        \DB::table('activity_analista')->delete();
        \DB::table('activity_statuses')->delete();

        // Elimina requerimientos, comentarios, correos, etc. si lo deseas
        \DB::table('requirements')->delete();
        \DB::table('comments')->delete();
        \DB::table('emails')->delete();

        // Finalmente elimina todas las actividades
        \DB::table('activities')->delete();
    }
}