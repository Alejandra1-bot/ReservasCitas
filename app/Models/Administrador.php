<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Administrador extends Model implements JWTSubject
{
    protected $table = 'administradores';
    protected $fillable = ['Nombre', 'Apellido', 'Documento', 'Telefono', 'Email', 'Password'];

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
