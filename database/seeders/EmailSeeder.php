<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\Email;

class EmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vacía la tabla antes de poblarla
        \DB::table('emails')->truncate();

        // Obtener muchas actividades existentes
        $activities = Activity::inRandomOrder()->take(1000)->get();

        if ($activities->count() > 0) {
            foreach ($activities as $activity) {
                // Crear algunos correos de ejemplo para cada actividad
                Email::create([
                    'activity_id' => $activity->id,
                    'type' => 'received',
                    'subject' => 'Solicitud de información - Caso ' . $activity->caso,
                    'sender_recipient' => 'cliente@empresa.com',
                    'content' => 'Estimados, necesitamos información adicional sobre el caso ' . $activity->caso . '. Por favor, proporcionen los detalles solicitados en el requerimiento inicial.',
                    'attachments' => ['documento_solicitud.pdf', 'anexo_tecnico.docx'],
                    'email_type' => 'informativo',
                ]);

                Email::create([
                    'activity_id' => $activity->id,
                    'type' => 'sent',
                    'subject' => 'Re: Solicitud de información - Caso ' . $activity->caso,
                    'sender_recipient' => 'cliente@empresa.com',
                    'content' => 'Estimado cliente, hemos recibido su solicitud y estamos procesando la información requerida. Le enviaremos una respuesta detallada en las próximas 48 horas.',
                    'attachments' => null,
                    'email_type' => 'respuesta',
                ]);

                Email::create([
                    'activity_id' => $activity->id,
                    'type' => 'sent',
                    'subject' => 'Entrega de documentación - Caso ' . $activity->caso,
                    'sender_recipient' => 'cliente@empresa.com',
                    'content' => 'Adjuntamos la documentación solicitada para el caso ' . $activity->caso . '. Por favor, revisen los archivos y confirmen la recepción.',
                    'attachments' => ['informe_final.pdf', 'analisis_tecnico.xlsx', 'recomendaciones.docx'],
                    'email_type' => 'entrega',
                ]);
            }
        }
    }
}
