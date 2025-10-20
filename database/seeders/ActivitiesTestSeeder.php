<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Analista;
use Illuminate\Support\Facades\DB;



class ActivitiesTestSeeder extends Seeder
{
    public function run()
    {
        // Vacía la tabla antes de poblarla
        DB::table('activities')->truncate();

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

        // Obtén todos los IDs de proyectos existentes
        $proyectoIds = \App\Models\Proyecto::pluck('id')->toArray();

        // Obtén todos los IDs de clientes existentes
        $clienteIds = \App\Models\Cliente::pluck('id')->toArray();

        // Frases para estatus_operacional
        $estatusOperacionalList = [
            'Enviado pases a Master',
            'Entregado para certificar',
            'Esperando archivos de as400',
            'En revisión por el cliente',
            'Pendiente de documentación',
            'Listo para producción'
        ];

        // Depuración: Verifica que los arrays no estén vacíos
        if (empty($proyectoIds)) {
            \Log::warning('No hay proyectos en la base de datos, proyecto_id será null en activities');
        }
        if (empty($estatusOperacionalList)) {
            \Log::warning('No hay frases para estatus_operacional');
        }

        // Crea 100 actividades principales
        for ($i = 1; $i <= 100; $i++) {
            // Elige un cliente aleatorio para la actividad principal y sus subactividades
            $clienteId = !empty($clienteIds) ? $clienteIds[array_rand($clienteIds)] : null;

            $activity = Activity::create([
                'name' => "Actividad Principal $i",
                'description' => "Descripción de la actividad principal $i",
                'caso' => "CASO-" . str_pad($i, 3, '0', STR_PAD_LEFT),
                'prioridad' => rand(1, 5),
                'orden_analista' => rand(1, 5),
                'status' => $statusList[array_rand($statusList)],
                'fecha_recepcion' => Carbon::now()->subDays(rand(0, 365)),
                'parent_id' => null,
                'proyecto_id' => !empty($proyectoIds) ? $proyectoIds[array_rand($proyectoIds)] : 1,
                'estatus_operacional' => !empty($estatusOperacionalList) ? $estatusOperacionalList[array_rand($estatusOperacionalList)] : 'Sin estatus',
                'porcentaje_avance' => rand(1, 10) * 10,
                'cliente_id' => $clienteId,
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
                    'proyecto_id' => !empty($proyectoIds) ? $proyectoIds[array_rand($proyectoIds)] : null,
                    'estatus_operacional' => $estatusOperacionalList[array_rand($estatusOperacionalList)],
                    'porcentaje_avance' => rand(1, 10) * 10,
                    'cliente_id' => $clienteId,
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
                        'proyecto_id' => !empty($proyectoIds) ? $proyectoIds[array_rand($proyectoIds)] : null,
                        'estatus_operacional' => $estatusOperacionalList[array_rand($estatusOperacionalList)],
                        'porcentaje_avance' => rand(1, 10) * 10,
                        'cliente_id' => $clienteId,
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