<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proyecto;

class ProyectoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $proyectos = [
            'Sistema de Pagos Móviles',
            'Plataforma de Créditos',
            'App de Clientes',
            'Integración con Banco Central',
            'Portal de Proveedores',
            'Gestión de Nómina',
            'Modernización de Cajeros',
            'Sistema de Reportes',
            'Migración a la Nube',
            'Automatización de Procesos'
        ];

        foreach ($proyectos as $nombre) {
            Proyecto::create([
                'nombre' => $nombre
            ]);
        }
    }
}
