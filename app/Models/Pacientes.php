<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Pacientes extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'pacientes';

    protected $fillable = [
        'Nombre',
        'Apellido',
        'Documento',
        'Telefono',
        'Email',
        'Fecha_nacimiento',
        'Genero',
        'RH',
        'Nacionalidad',
        'password',
        'roles'
    ];

    protected $hidden = [
        'Password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'Password' => 'hashed',
        ];
    }

    // Relaciones
    public function citas()
    {
        return $this->hasMany(citas::class, 'idPaciente');
    }

    // Métodos JWT
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
