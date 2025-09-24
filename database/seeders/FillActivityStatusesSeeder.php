<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\Status;
use Illuminate\Support\Facades\DB;

class FillActivityStatusesSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = Status::pluck('id')->all();
        $now = now();

        Activity::chunk(100, function ($activities) use ($statuses, $now) {
            foreach ($activities as $activity) {
                // Elige 1 a 2 estados aleatorios para cada actividad
                $randomStatusIds = collect($statuses)->random(rand(1, 2))->all();
                foreach ($randomStatusIds as $statusId) {
                    // Evita duplicados
                    DB::table('activity_statuses')->updateOrInsert(
                        ['activity_id' => $activity->id, 'status_id' => $statusId],
                        ['created_at' => $now, 'updated_at' => $now]
                    );
                }
            }
        });
    }
}
php artisan db:seed --class=FillActivityStatusesSeeder
