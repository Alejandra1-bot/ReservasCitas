<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pacientes extends Model
{
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
        'Password'
    ];

     public function citas(){
        return $this->hasMany(citas::class, 'idPaciente');
    }
}
