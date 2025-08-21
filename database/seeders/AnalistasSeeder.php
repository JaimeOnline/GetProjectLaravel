<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Analista;

class AnalistasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $analistas = [
            'Gabriel Poleo',
            'José Poleo',
            'Luis Colmenarez',
            'Edgar Silva',
            'Luis Sosa',
            'Rodrigo Campos',
            'Luis Poleo',
        ];

        foreach ($analistas as $nombre) {
            Analista::create([
                'name' => $nombre
            ]);
        }
    }
}
