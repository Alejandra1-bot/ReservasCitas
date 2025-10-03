<?php

namespace App\Console\Commands;

use App\Models\Pacientes;
use App\Models\Medicos;
use App\Models\Administrador;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class HashExistingPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:hash-existing-passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hash existing plain text passwords in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Migrating users to users table...');

        // Migrar pacientes
        Pacientes::all()->each(function ($paciente) {
            if (!User::where('email', $paciente->Email)->exists()) {
                User::create([
                    'name' => $paciente->Nombre,
                    'apellido' => $paciente->Apellido,
                    'documento' => $paciente->Documento,
                    'telefono' => $paciente->Telefono,
                    'email' => $paciente->Email,
                    'fechaNacimiento' => $paciente->Fecha_nacimiento,
                    'genero' => $paciente->Genero,
                    'rh' => $paciente->RH,
                    'nacionalidad' => $paciente->Nacionalidad,
                    'password' => $paciente->password,
                    'rol' => 'paciente',
                ]);
            }
        });

        // Migrar medicos
        Medicos::all()->each(function ($medico) {
            if (!User::where('email', $medico->Email)->exists()) {
                User::create([
                    'name' => $medico->Nombre,
                    'apellido' => $medico->Apellido,
                    'documento' => $medico->Documento,
                    'telefono' => $medico->Telefono,
                    'email' => $medico->Email,
                    'password' => $medico->Password,
                    'rol' => 'medico',
                ]);
            }
        });

        // Migrar administradores
        Administrador::all()->each(function ($admin) {
            if (!User::where('email', $admin->Email)->exists()) {
                User::create([
                    'name' => $admin->Nombre,
                    'apellido' => $admin->Apellido,
                    'documento' => $admin->Documento,
                    'telefono' => $admin->Telefono,
                    'email' => $admin->Email,
                    'password' => $admin->Password,
                    'rol' => 'administrador',
                ]);
            }
        });

        $this->info('Users migrated to users table.');
    }
}
