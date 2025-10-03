<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Medicos extends Model implements JWTSubject
{
     protected $table = 'Medicos';
    protected $fillable = [
        'Nombre',
        'Apellido',
        'Documento',
        'Telefono',
        'Email',
        'Password',
        'idConsultorio',
        'idEspecialidad'

    ];

     public function citas(){
        return $this->hasMany(citas::class, 'idMedico');
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
