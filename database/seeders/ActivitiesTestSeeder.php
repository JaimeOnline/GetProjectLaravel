<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\User; 
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Analista; 


class ActivitiesTestSeeder extends Seeder
{
    public function run()
    {

        // Crea algunos analistas de prueba si no existen
        $analistas = [];
        for ($i = 1; $i <= 6; $i++) {
            $analistas[] = Analista::firstOrCreate(
                ['name' => "Analista $i"]
            );
        }
        $analistaIds = collect($analistas)->pluck('id')->toArray();


        $statusList = [
            'no_iniciada',
            'en_ejecucion',
            'culminada',
            'en_espera_de_insumos',
            'en_certificacion_por_cliente',
            'pases_enviados',
            'pausada',
            'cancelada'
        ];

        // Crea 20 actividades principales
        for ($i = 1; $i <= 20; $i++) {
            $activity = Activity::create([
                'name' => "Actividad Principal $i",
                'description' => "Descripción de la actividad principal $i",
                'caso' => "CASO-" . str_pad($i, 3, '0', STR_PAD_LEFT),
                'prioridad' => rand(1, 5),
                'orden_analista' => rand(1, 5),
                'status' => $statusList[array_rand($statusList)],
                'fecha_recepcion' => Carbon::now()->subDays(rand(0, 365)),
                'parent_id' => null,
            ]);

            // Asigna analistas aleatorios
            $activity->analistas()->sync(
                collect($analistaIds)->random(rand(1, 3))->all()
            );

            // Crea requerimientos
            for ($r = 1; $r <= rand(1, 5); $r++) {
                $activity->requirements()->create([
                    'description' => "Requerimiento $r de Actividad $i",
                    'status' => rand(0, 1) ? 'pendiente' : 'recibido',
                    'created_at' => Carbon::now()->subDays(rand(0, 365)),
                ]);
            }

            // Subactividades
            for ($j = 1; $j <= rand(2, 4); $j++) {
                $sub = Activity::create([
                    'name' => "Subactividad $j de $i",
                    'description' => "Descripción de la subactividad $j de $i",
                    'caso' => "CASO-$i-SUB$j",
                    'prioridad' => rand(1, 5),
                    'orden_analista' => rand(1, 5),
                    'status' => $statusList[array_rand($statusList)],
                    'fecha_recepcion' => Carbon::now()->subDays(rand(0, 365)),
                    'parent_id' => $activity->id,
                ]);
                $sub->analistas()->sync(
                    collect($analistaIds)->random(rand(1, 2))->all()
                );
                for ($r = 1; $r <= rand(1, 3); $r++) {
                    $sub->requirements()->create([
                        'description' => "Requerimiento $r de Subactividad $j de $i",
                        'status' => rand(0, 1) ? 'pendiente' : 'recibido',
                        'created_at' => Carbon::now()->subDays(rand(0, 365)),
                    ]);
                }

                // Subsubactividades
                for ($k = 1; $k <= rand(1, 2); $k++) {
                    $subsub = Activity::create([
                        'name' => "Subsubactividad $k de $j de $i",
                        'description' => "Descripción de la subsubactividad $k de $j de $i",
                        'caso' => "CASO-$i-SUB$j-SUB$k",
                        'prioridad' => rand(1, 5),
                        'orden_analista' => rand(1, 5),
                        'status' => $statusList[array_rand($statusList)],
                        'fecha_recepcion' => Carbon::now()->subDays(rand(0, 365)),
                        'parent_id' => $sub->id,
                    ]);
                    $subsub->analistas()->sync(
                        collect($analistaIds)->random(rand(1, 2))->all()
                    );
                    for ($r = 1; $r <= rand(1, 2); $r++) {
                        $subsub->requirements()->create([
                            'description' => "Requerimiento $r de Subsubactividad $k de $j de $i",
                            'status' => rand(0, 1) ? 'pendiente' : 'recibido',
                            'created_at' => Carbon::now()->subDays(rand(0, 365)),
                        ]);
                    }
                }
            }
        }
    }
}

/* EJECUTA EN LA TERMINAL PARA INSERTAR UNA NUEVA BASE DE DATOS DE PRUEBA
php artisan migrate:fresh --seed */