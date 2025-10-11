<?php

namespace App\Http\Controllers;

use App\Models\Pacientes;
use App\Models\Medicos;
use App\Models\Administrador;
use App\Models\Resepcionistas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function registrar(Request $request)
    {
        $validator = Validator::make($request->all(),[
        'Nombre'=> 'required|string',
        'Apellido'=> 'required|string',
        'Documento'=> 'required|string',
        'Telefono'=> 'required|string',
        'Email' => 'required_without:email|string',
        'Fecha_nacimiento'=> 'date',
        'Genero'=> 'nullable|in:M,F',
        'RH'=> 'string',
        'Nacionalidad'=> 'string',
        'password'=> 'required|string',
        'roles'=> 'required|in:medico,paciente,administrador,recepcionista',
        'idConsultorio' => 'required_if:roles,medico|integer',
        'idEspecialidad' => 'required_if:roles,medico|integer',
        'Turno' => 'required_if:roles,recepcionista|string',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
// Crear usuario en tabla users
        $user = User::create([
            'name' => $request->Nombre,
            'apellido' => $request->Apellido,
            'documento' => $request->Documento,
            'telefono' => $request->Telefono,
            'email' => $request->Email ?? $request->email,
            'fechaNacimiento' => $request->Fecha_nacimiento,
            'genero' => $request->Genero,
            'rh' => $request->RH,
            'nacionalidad' => $request->Nacionalidad,
            'password' => Hash::make($request->password),
            'rol' => $request->roles,
        ]);

        // Crear en tabla específica
        if ($request->roles === 'paciente') {
            Pacientes::create([
                'Nombre' =>  $request->Nombre,
                'Apellido' =>  $request->Apellido,
                'Documento' =>  $request->Documento,
                'Telefono' =>  $request->Telefono,
                'Email' =>  $request->email,
                'Fecha_nacimiento'=> $request->Fecha_nacimiento,
                'Genero' =>  $request->Genero,
                'RH' =>  $request->RH,
                'Nacionalidad' =>  $request->Nacionalidad,
                'password' => Hash::make($request->password),
            ]);
        } elseif ($request->roles === 'medico') {
            Medicos::create([
                'Nombre' => $request->Nombre,
                'Apellido' => $request->Apellido,
                'Documento' => $request->Documento,
                'Telefono' => $request->Telefono,
                'Email' => $request->Email ?? $request->email,
                'Password' => Hash::make($request->password),
                'idConsultorio' => $request->idConsultorio,
                'idEspecialidad' => $request->idEspecialidad,
            ]);
        } elseif ($request->roles === 'administrador') {
            Administrador::create([
                'Nombre' => $request->Nombre,
                'Apellido' => $request->Apellido,
                'Documento' => $request->Documento,
                'Telefono' => $request->Telefono,
                'Email' =>  $request->Email ?? $request->email,
                'Password' => Hash::make($request->password),
            ]);
        } elseif ($request->roles === 'recepcionista') {
            Resepcionistas::create([
                'Nombre' => $request->Nombre,
                'Apellido' => $request->Apellido,
                'Turno' => $request->Turno,
                'Telefono' => $request->Telefono,
                'Email' =>  $request->Email ?? $request->email,
                'Password' => Hash::make($request->password),
            ]);
        }

    
             
        try{
            $token = JWTAuth::fromUser($user);
            return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo crear el Token JWT',
                'error' => $e->getMessage(),
            ], 500);

        }
    } 

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Email' => 'required_without:email|string',
            'email' => 'required_without:Email|string',
            'password' => 'required|string|min:8',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $request->Email ?? $request->email;
        $password = $request->password;

        $user = User::where('email', $email)->first();
        if ($user && Hash::check($password, $user->password)) {
            $token = JWTAuth::customClaims(['role' => $user->rol])->fromUser($user);
            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'role' => $user->rol,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Credenciales inválidas',
        ], 401);
    }

    public function logout(){
        try{
            $pacientes = JWTAuth::pacientes(); // validar el usuario logeado
            JWTAuth::invalidate(JWTAuth::getToken()); // invalidar el token
            return response()->json([
                'success' => true,
                'message' => $pacientes->name.' ha cerrado sesion correctamente',
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => ' Error al  cerrar la sesion',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function me ()
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado',
                ], 404);
            }

            $rol = $user->rol;
            $data = $user->toArray();

            if ($rol === 'paciente') {
                $paciente = Pacientes::where('Email', $user->email)->first();
                if ($paciente) {
                    $data = array_merge($data, $paciente->toArray());
                }
            } elseif ($rol === 'medico') {
                $medico = Medicos::where('Email', $user->email)->first();
                if ($medico) {
                    $data = array_merge($data, $medico->toArray());
                }
            } elseif ($rol === 'administrador') {
                $admin = Administrador::where('Email', $user->email)->first();
                if ($admin) {
                    $data = array_merge($data, $admin->toArray());
                }
            } elseif ($rol === 'recepcionista') {
                $recepcionista = Resepcionistas::where('Email', $user->email)->first();
                if ($recepcionista) {
                    $data = array_merge($data, $recepcionista->toArray());
                }
            }

            // Rename fields
            $data['nombre'] = $data['name'] ?? $data['Nombre'] ?? 'No disponible';
            unset($data['name'], $data['Nombre']);
            $data['fecha_nacimiento'] = $data['fechaNacimiento'] ?? $data['Fecha_nacimiento'] ?? 'No disponible';
            unset($data['fechaNacimiento'], $data['Fecha_nacimiento']);

            // Agregar citas si es paciente
            if ($rol === 'paciente') {
                $citas = \App\Models\Citas::all();
                $data['citas'] = $citas;
            }

            return response()->json([
                'success' => true,
                'user' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido',
                'error' => $e->getMessage(),
            ], 401);
        }
    }
    


}

