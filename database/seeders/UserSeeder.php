<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
class UserSeeder extends Seeder
{
    public function run()
    {
        // Agregar usuarios
        User::create([
            'name' => 'Arcangel Poleo',
            'email' => 'arcangel@example.com', // Cambia esto por el correo que desees
            'password' => bcrypt('password123'), // Cambia la contraseña según sea necesario
        ]);
        // Puedes agregar más usuarios aquí si lo deseas
        User::create([
            'name' => 'José Poleo',
            'email' => 'otro@example.com',
            'password' => bcrypt('password123'),
        ]);
    }
}

//PARA AGREGAR LOS USUARIOS, EJECUTA EL SIGUIENTE COMANDO EN LA TERMINAL
// php artisan db:seed --class=UserSeeder