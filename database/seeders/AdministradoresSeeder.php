<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Administrador;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdministradoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear administrador en tabla específica
        $admin = Administrador::create([
            'Nombre' => 'Admin',
            'Apellido' => 'Sistema',
            'Documento' => '123456789',
            'Telefono' => '3001234567',
            'Email' => 'admin@reservasmedicas.com',
            'Password' => Hash::make('admin123'),
        ]);

        // Crear usuario en tabla users
        User::create([
            'name' => 'Admin',
            'Apellido' => 'Sistema',
            'Documento' => '123456789',
            'Telefono' => '3001234567',
            'email' => 'admin@reservasmedicas.com',
            'fechaNacimiento' => null,
            'genero' => null,
            'rh' => null,
            'nacionalidad' => null,
            'password' => Hash::make('admin123'),
            'rol' => 'administrador',
        ]);
    }
}
