<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // database/seeders/TipoProductoSeeder.php
public function run()
{
    \App\Models\TipoProducto::create(['nombre' => 'Alto Valor']);
    \App\Models\TipoProducto::create(['nombre' => 'Crédito Inmediato']);
    \App\Models\TipoProducto::create(['nombre' => 'Débito Inmediato']);
    \App\Models\TipoProducto::create(['nombre' => 'Pago Nómina y Prvedores']);
    \App\Models\TipoProducto::create(['nombre' => 'App Móvil']);
}

}
