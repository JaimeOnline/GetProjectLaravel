<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        // Limpia tablas si es necesario (opcional)
        // $this->call(CleanActivitiesSeeder::class);

        $this->call([
            UserSeeder::class,
            ClienteSeeder::class,
            TipoProductoSeeder::class,
            ProyectoSeeder::class,
            AnalistasSeeder::class,
            StatusSeeder::class,
            ActivitiesTestSeeder::class,
            CommentSeeder::class,
            EmailSeeder::class,
            FillActivityStatusesSeeder::class,
        ]);
    }
}
