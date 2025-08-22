<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especialidades extends Model

{
    protected $table = 'especialidades';
    protected $fillable = [
        'Nombre',
        'Descripcion'
    
    ];

     public function medicos(){
        return $this->hasMany(medicos::class, 'idEspecialidad');
    }
}
