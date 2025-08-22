<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resepcionistas extends Model
{
  protected $table = 'resepcionistas';
    protected $fillable = [
            'Nombre',
            'Apellido',
            'Turno',
            'Telefono',
            'Email',
            'Password'

    ];

     public function citas(){
        return $this->hasMany(citas::class, 'idResepcionista');
    }
}
