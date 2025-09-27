<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   // database/seeders/ClienteSeeder.php
public function run()
{
    \App\Models\Cliente::create(['nombre' => 'BT Banco del Tesoro']);
    \App\Models\Cliente::create(['nombre' => 'BDT Banco Digital de los Trabajadores']);
}

}
