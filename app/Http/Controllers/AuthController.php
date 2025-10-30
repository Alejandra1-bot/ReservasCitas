<?php

namespace App\Http\Controllers;

use App\Models\Pacientes;
use App\Models\Medicos;
use App\Models\Administrador;
use App\Models\Resepcionistas;
use App\Models\User;
use App\Mail\ResetPasswordMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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
// Crear en tabla específica primero
if ($request->roles === 'paciente') {
    Pacientes::create([
        'Nombre' =>  $request->Nombre,
        'Apellido' =>  $request->Apellido,
        'Documento' =>  $request->Documento,
        'Telefono' =>  $request->Telefono,
        'Email' =>  $request->Email ?? $request->email,
        'Fecha_nacimiento'=> $request->Fecha_nacimiento,
        'Genero' =>  $request->Genero,
        'RH' =>  $request->RH,
        'Nacionalidad' =>  $request->Nacionalidad,
        'password' => Hash::make($request->password),
    ]);
} elseif ($request->roles === 'medico') {
    Medicos::create([
        'Nombre' =>  $request->Nombre,
        'Apellido' =>  $request->Apellido,
        'Documento' =>  $request->Documento,
        'Telefono' =>  $request->Telefono,
        'Email' =>  $request->Email ?? $request->email,
        'Password' => Hash::make($request->password),
        'idConsultorio' => $request->idConsultorio,
        'idEspecialidad' => $request->idEspecialidad,
    ]);
} elseif ($request->roles === 'administrador') {
    Administrador::create([
        'Nombre' =>  $request->Nombre,
        'Apellido' =>  $request->Apellido,
        'Documento' =>  $request->Documento,
        'Telefono' =>  $request->Telefono,
        'Email' =>  $request->Email ?? $request->email,
        'Password' => Hash::make($request->password),
    ]);
} elseif ($request->roles === 'recepcionista') {
    Resepcionistas::create([
        'Nombre' =>  $request->Nombre,
        'Apellido' =>  $request->Apellido,
        'Turno' =>  $request->Turno,
        'Telefono' =>  $request->Telefono,
        'Email' =>  $request->Email ?? $request->email,
        'Password' => Hash::make($request->password),
    ]);
}

// Crear usuario en tabla users (después de crear el registro específico)
$user = User::create([
    'name' => $request->Nombre,
    'Apellido' => $request->Apellido,
    'Documento' => $request->Documento,
    'Telefono' => $request->Telefono,
    'email' => $request->Email ?? $request->email,
    'fechaNacimiento' => $request->Fecha_nacimiento,
    'genero' => $request->Genero,
    'rh' => $request->RH,
    'nacionalidad' => $request->Nacionalidad,
    'password' => Hash::make($request->password),
    'rol' => $request->roles,
]);

    
             
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

    public function updatePassword(Request $request)
{
    $validator = Validator::make($request->all(), [
        'current_password' => 'required|string',
        'new_password' => 'required|string|min:8|confirmed',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
    }

    $user = auth()->user();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Usuario no autenticado',
        ], 401);
    }

    if (!Hash::check($request->current_password, $user->password)) {
        return response()->json([
            'success' => false,
            'message' => 'La contraseña actual es incorrecta',
        ], 400);
    }

    $user->password = Hash::make($request->new_password);
    $user->save();

    // Actualizar en la tabla específica según el rol
    $rol = $user->rol;
    if ($rol === 'paciente') {
        $paciente = Pacientes::where('Email', $user->email)->first();
        if ($paciente) {
            $paciente->password = Hash::make($request->new_password);
            $paciente->save();
        }
    } elseif ($rol === 'medico') {
        $medico = Medicos::where('Email', $user->email)->first();
        if ($medico) {
            $medico->Password = Hash::make($request->new_password);
            $medico->save();
        }
    } elseif ($rol === 'administrador') {
        $admin = Administrador::where('Email', $user->email)->first();
        if ($admin) {
            $admin->Password = Hash::make($request->new_password);
            $admin->save();
        }
    } elseif ($rol === 'recepcionista') {
        $recepcionista = Resepcionistas::where('Email', $user->email)->first();
        if ($recepcionista) {
            $recepcionista->Password = Hash::make($request->new_password);
            $recepcionista->save();
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Contraseña actualizada correctamente',
    ], 200);
    }

    public function recuperarContrasena(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $request->email;

        // Verificar si el email existe en alguna tabla
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'El email no está registrado',
            ], 404);
        }

        // Generar código de 6 dígitos numérico
        $token = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Guardar token en la base de datos
        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            ['token' => $token, 'created_at' => now()]
        );

        // Enviar email
        try {
            Mail::to($email)->send(new ResetPasswordMail($token, $email));
            return response()->json([
                'success' => true,
                'message' => 'Se ha enviado un enlace de recuperación a tu email',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el email',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:6|regex:/^[0-9]+$/',
            'newPassword' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $token = $request->code;
        $password = $request->newPassword;

        // Verificar código (sin email, solo por código)
        $reset = DB::table('password_resets')
            ->where('token', $token)
            ->where('created_at', '>', now()->subHours(1)) // Código válido por 1 hora
            ->first();

        if (!$reset) {
            return response()->json([
                'success' => false,
                'message' => 'Código inválido o expirado',
            ], 400);
        }

        $email = $reset->email; // Obtener email del registro encontrado

        // Actualizar contraseña en tabla users
        $user = User::where('email', $email)->first();
        if ($user) {
            $user->password = Hash::make($password);
            $user->save();
        }

        // Actualizar en tabla específica según rol
        $rol = $user->rol;
        if ($rol === 'paciente') {
            $paciente = Pacientes::where('Email', $email)->first();
            if ($paciente) {
                $paciente->password = Hash::make($password);
                $paciente->save();
            }
        } elseif ($rol === 'medico') {
            $medico = Medicos::where('Email', $email)->first();
            if ($medico) {
                $medico->Password = Hash::make($password);
                $medico->save();
            }
        } elseif ($rol === 'administrador') {
            $admin = Administrador::where('Email', $email)->first();
            if ($admin) {
                $admin->Password = Hash::make($password);
                $admin->save();
            }
        } elseif ($rol === 'recepcionista') {
            $recepcionista = Resepcionistas::where('Email', $email)->first();
            if ($recepcionista) {
                $recepcionista->Password = Hash::make($password);
                $recepcionista->save();
            }
        }

        // Eliminar código usado
        DB::table('password_resets')->where('token', $token)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contraseña restablecida correctamente',
        ], 200);
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

        $user = User::where('Email', $email)->first();
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

    public function updateUsuario(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Nombre' => 'nullable|string',
            'Apellido' => 'nullable|string',
            'Documento' => 'nullable|string',
            'Telefono' => 'nullable|string',
            'Fecha_nacimiento' => 'nullable|date',
            'Genero' => 'nullable|in:M,F',
            'RH' => 'nullable|string',
            'Nacionalidad' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado',
            ], 401);
        }

        // Actualizar en tabla users
        $user->update($request->only(['Nombre', 'Apellido', 'Documento', 'Telefono', 'fechaNacimiento', 'genero', 'rh', 'nacionalidad']));

        // Actualizar en tabla específica según rol
        $rol = $user->rol;
        if ($rol === 'paciente') {
            $paciente = Pacientes::where('Email', $user->Email)->first();
            if ($paciente) {
                $paciente->update($request->only(['Nombre', 'Apellido', 'Documento', 'Telefono', 'Fecha_nacimiento', 'Genero', 'RH', 'Nacionalidad']));
            }
        } elseif ($rol === 'medico') {
            $medico = Medicos::where('Email', $user->Email)->first();
            if ($medico) {
                $medico->update($request->only(['Nombre', 'Apellido', 'Documento', 'Telefono']));
            }
        } elseif ($rol === 'administrador') {
            $admin = Administrador::where('Email', $user->Email)->first();
            if ($admin) {
                $admin->update($request->only(['Nombre', 'Apellido', 'Documento', 'Telefono']));
            }
        } elseif ($rol === 'recepcionista') {
            $recepcionista = Resepcionistas::where('Email', $user->Email)->first();
            if ($recepcionista) {
                $recepcionista->update($request->only(['Nombre', 'Apellido', 'Telefono']));
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado correctamente',
        ], 200);
    }


}

