<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    public function run()
    {
        Status::updateOrCreate(
            ['name' => 'reiterar'],
            [
                'label' => 'Reiterar',
                'color' => '#ff5722',
                'icon' => 'redo',
                'order' => 9,
                'is_active' => 1,
            ]
        );

        Status::updateOrCreate(
            ['name' => 'atendiendo_hoy'],
            [
                'label' => 'Atendiendo hoy',
                'color' => '#007bff',
                'icon' => 'calendar-day',
                'order' => 10,
                'is_active' => 1,
            ]
        );
    }
}
