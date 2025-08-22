<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Citas extends Model
{
    protected $table = 'citas';
    protected $fillable = [
        'Fecha_cita',
        'Hora',
        'Estado',
        'idPaciente',
        'idMedico',
        'idResepcionista',

        
    ];

    
}
