<?php

use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PacientesController;
use App\Http\Controllers\ConsultoriosController;
use App\Http\Controllers\EspecialidadesController;
use App\Http\Controllers\ResepcionistasController;
use App\Http\Controllers\MedicosController;
use App\Http\Controllers\CitasController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\RoleMiddleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

    Route::middleware('jwt.auth')->group(function (){});

    Route::post('registrar', [AuthController::class, 'registrar']);
    Route::post('login', [AuthController::class, 'login']);

    Route::group(['middleware' => 'auth:api'], function () {
         Route::get('me', [AuthController::class, 'me']);
         Route::post('logout', [AuthController::class, 'logout']);

        Route::group(['middleware'=>RoleMiddleware::class.':admin'],function(){
            Route::post('CrearConsultorios', [ConsultoriosController::class, 'store' ]);
            Route::put('actualizarConsultorios/{id}', [ConsultoriosController::class, 'update' ]);
            Route::delete('eliminarConsultorios/{id}', [ConsultoriosController::class, 'destroy' ]);
            // Route::post('CrearEspecialidades', [EspecialidadesController::class, 'store' ]);
            Route::put('actualizarEspecialidades/{id}', [EspecialidadesController::class, 'update' ]);
            Route::delete('eliminarEspecialidades/{id}', [EspecialidadesController::class, 'destroy' ]);
            // Route::post('CrearMedicos', [MedicosController::class, 'store' ]);
            Route::put('actualizarMedicos/{id}', [MedicosController::class, 'update' ]);
            Route::delete('eliminarMedicos/{id}', [MedicosController::class, 'destroy' ]);


        });

        // Route::middleware(['auth:api', 'role:admin,'])->group(function (){
        //  });
    });
    

// Rutas para Pacientes 
Route::get('listarPacientes', [PacientesController::class, 'index' ]);
Route::post('CrearPacientes', [PacientesController::class, 'store' ]);
Route::get('pacientes/{id}', [PacientesController::class, 'show' ]);
Route::put('actualizarPacientes/{id}', [PacientesController::class, 'update' ]);
Route::delete('eliminarPacientes/{id}', [PacientesController::class, 'destroy' ]);


// Rutas para consultorios 
Route::get('listarConsultorios', [ConsultoriosController::class, 'index' ]);
// Route::post('CrearConsultorios', [ConsultoriosController::class, 'store' ]);
// Route::get('consultorios/{id}', [ConsultoriosController::class, 'show' ]);
// Route::put('actualizarConsultorios/{id}', [ConsultoriosController::class, 'update' ]);
// Route::delete('eliminarConsultorios/{id}', [ConsultoriosController::class, 'destroy' ]);

// Rutas para especialidades 
Route::get('listarEspecialidades', [EspecialidadesController::class, 'index' ]);
Route::post('CrearEspecialidades', [EspecialidadesController::class, 'store' ]);
// Route::get('Especialidades/{id}', [EspecialidadesController::class, 'show' ]);
// Route::put('actualizarEspecialidades/{id}', [EspecialidadesController::class, 'update' ]);
// Route::delete('eliminarEspecialidades/{id}', [EspecialidadesController::class, 'destroy' ]);

// Rutas para resepcionistas 
Route::get('listarResepcionistas', [ResepcionistasController::class, 'index' ]);
Route::post('CrearResepcionistas', [ResepcionistasController::class, 'store' ]);
Route::get('Resepcionistas/{id}', [ResepcionistasController::class, 'show' ]);
Route::put('actualizarResepcionistas/{id}', [ResepcionistasController::class, 'update' ]);
Route::delete('eliminarResepcionistas/{id}', [ResepcionistasController::class, 'destroy' ]);


// Rutas para medicos 
Route::get('listarMedicos', [MedicosController::class, 'index' ]);
Route::post('CrearMedicos', [MedicosController::class, 'store' ]);
// Route::get('Medicos/{id}', [MedicosController::class, 'show' ]);
// Route::put('actualizarMedicos/{id}', [MedicosController::class, 'update' ]);
// Route::delete('eliminarMedicos/{id}', [MedicosController::class, 'destroy' ]);


// Rutas para citas 
Route::get('listarCitas', [CitasController::class, 'index' ]);
Route::post('CrearCitas', [CitasController::class, 'store' ]);
Route::get('Citas/{id}', [CitasController::class, 'show' ]);
Route::put('actualizarCitas/{id}', [CitasController::class, 'update' ]);
Route::delete('eliminarCitas/{id}', [CitasController::class, 'destroy' ]);

// // temporales 
// //Contar pacientes por género
// Route::get('/sql/pacientes/contar-genero', function () {
//     $rows = DB::select("SELECT Genero, COUNT(*) as total FROM pacientes GROUP BY Genero");
//     return response()->json($rows);
// });

// //2. Pacientes ordenados por fecha de nacimiento (mayores primero)
// Route::get('/sql/pacientes/ordenados/edad', function () {
//     $rows = DB::select("SELECT * FROM pacientes ORDER BY Fecha_nacimiento ASC");
//     return response()->json($rows);
// });


// // 3. Buscar pacientes por nacionalidad
// Route::get('/sql/pacientes/nacionalidad/{nac}', function ($nac) {
//     $rows = DB::select("SELECT * FROM pacientes WHERE Nacionalidad = ?", [$nac]);
//     return response()->json($rows);
// });

// // 6. Buscar por email (like, coincidencias parciales)
// Route::get('/sql/pacientes/email/{texto}', function ($texto) {
//     $rows = DB::select("SELECT * FROM pacientes WHERE Email LIKE ?", ["%$texto%"]);
//     return response()->json($rows);
// });

// // 10. Obtener solo nombre y apellido (más ligero)
// Route::get('/sql/pacientes/nombres', function () {
//     $rows = DB::select("SELECT Nombre, Apellido FROM pacientes");
//     return response()->json($rows);
// });