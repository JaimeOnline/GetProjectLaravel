<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\Comment;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener algunas actividades existentes
        $activities = Activity::take(3)->get();

        foreach ($activities as $activity) {
            // Crear 2-3 comentarios por actividad
            for ($i = 1; $i <= rand(2, 3); $i++) {
                Comment::create([
                    'activity_id' => $activity->id,
                    'comment' => "Este es el comentario número {$i} para la actividad {$activity->name}. Contiene información relevante sobre el progreso y estado actual.",
                    'created_at' => now()->subDays(rand(1, 10))->subHours(rand(1, 23)),
                ]);
            }
        }
    }
}
