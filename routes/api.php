<?php

use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PacientesController;
use App\Http\Controllers\ConsultoriosController;
use App\Http\Controllers\EspecialidadesController;
use App\Http\Controllers\ResepcionistasController;
use App\Http\Controllers\MedicosController;
use App\Http\Controllers\CitasController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas para Pacientes 
Route::get('listarPacientes', [PacientesController::class, 'index' ]);
Route::post('CrearPacientes', [PacientesController::class, 'store' ]);
Route::get('pacientes/{id}', [PacientesController::class, 'show' ]);
Route::put('actualizarPacientes/{id}', [PacientesController::class, 'update' ]);
Route::delete('eliminarPacientes/{id}', [PacientesController::class, 'destroy' ]);


// Rutas para consultorios 
Route::get('listarConsultorios', [ConsultoriosController::class, 'index' ]);
Route::post('CrearConsultorios', [ConsultoriosController::class, 'store' ]);
Route::get('consultorios/{id}', [ConsultoriosController::class, 'show' ]);
Route::put('actualizarConsultorios/{id}', [ConsultoriosController::class, 'update' ]);
Route::delete('eliminarConsultorios/{id}', [ConsultoriosController::class, 'destroy' ]);

// Rutas para especialidades 
Route::get('listarEspecialidades', [EspecialidadesController::class, 'index' ]);
Route::post('CrearEspecialidades', [EspecialidadesController::class, 'store' ]);
Route::get('Especialidades/{id}', [EspecialidadesController::class, 'show' ]);
Route::put('actualizarEspecialidades/{id}', [EspecialidadesController::class, 'update' ]);
Route::delete('eliminarEspecialidades/{id}', [EspecialidadesController::class, 'destroy' ]);

// Rutas para resepcionistas 
Route::get('listarResepcionistas', [ResepcionistasController::class, 'index' ]);
Route::post('CrearResepcionistas', [ResepcionistasController::class, 'store' ]);
Route::get('Resepcionistas/{id}', [ResepcionistasController::class, 'show' ]);
Route::put('actualizarResepcionistas/{id}', [ResepcionistasController::class, 'update' ]);
Route::delete('eliminarResepcionistas/{id}', [ResepcionistasController::class, 'destroy' ]);


// Rutas para medicos 
Route::get('listarMedicos', [MedicosController::class, 'index' ]);
Route::post('CrearMedicos', [MedicosController::class, 'store' ]);
Route::get('Medicos/{id}', [MedicosController::class, 'show' ]);
Route::put('actualizarMedicos/{id}', [MedicosController::class, 'update' ]);
Route::delete('eliminarMedicos/{id}', [MedicosController::class, 'destroy' ]);


// Rutas para citas 
Route::get('listarCitas', [CitasController::class, 'index' ]);
Route::post('CrearCitas', [CitasController::class, 'store' ]);
Route::get('Citas/{id}', [CitasController::class, 'show' ]);
Route::put('actualizarCitas/{id}', [CitasController::class, 'update' ]);
Route::delete('eliminarCitas/{id}', [CitasController::class, 'destroy' ]);



Route::get('/usuarios', [UsuarioController::class, 'listar']);
