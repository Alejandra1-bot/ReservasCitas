<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicos extends Model
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
}
