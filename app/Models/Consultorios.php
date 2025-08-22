<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consultorios extends Model
{
    protected $table = 'consultorios';
    protected $fillable = [
        'Nombre',
        'Direccion',
        'Ciudad',
        'Telefono'
    
    ];

     public function medicos(){    
        return $this->hasMany(Medicos::class, 'idConsultorio');
     }

}
