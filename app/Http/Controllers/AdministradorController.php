<?php

namespace App\Http\Controllers;

use App\Models\Administrador;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdministradorController extends Controller
{
    /**
     * Mostrar todos los administradores.
     */
    public function index()
    {
        $administradores = Administrador::all();
        return response()->json([
            'success' => true,
            'data' => $administradores,
        ], 200);
    }

    /**
     * Mostrar un administrador específico.
     */
    public function show($id)
    {
        $administrador = Administrador::find($id);
        if (!$administrador) {
            return response()->json([
                'success' => false,
                'message' => 'Administrador no encontrado',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $administrador,
        ], 200);
    }

    /**
     * Crear un nuevo administrador.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Nombre' => 'required|string|max:255',
            'Apellido' => 'required|string|max:255',
            'Documento' => 'required|string|max:255|unique:administradores,Documento|unique:users,Documento',
            'Telefono' => 'required|string|max:255',
            'Email' => 'required|string|email|max:255|unique:administradores,Email|unique:users,email',
            'Password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Crear usuario en tabla users
            $user = User::create([
                'name' => $request->Nombre,
                'Apellido' => $request->Apellido,
                'Documento' => $request->Documento,
                'Telefono' => $request->Telefono,
                'email' => $request->Email,
                'fechaNacimiento' => null,
                'genero' => null,
                'rh' => null,
                'nacionalidad' => null,
                'password' => Hash::make($request->Password),
                'rol' => 'administrador',
            ]);

            // Crear administrador en tabla administradores
            $administrador = Administrador::create([
                'Nombre' => $request->Nombre,
                'Apellido' => $request->Apellido,
                'Documento' => $request->Documento,
                'Telefono' => $request->Telefono,
                'Email' => $request->Email,
                'Password' => Hash::make($request->Password),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Administrador creado correctamente',
                'data' => $administrador,
                'user' => $user,
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el administrador',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Actualizar un administrador existente.
     */
    public function update(Request $request, $id)
    {
        $administrador = Administrador::find($id);
        if (!$administrador) {
            return response()->json([
                'success' => false,
                'message' => 'Administrador no encontrado',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'Nombre' => 'nullable|string|max:255',
            'Apellido' => 'nullable|string|max:255',
            'Documento' => 'nullable|string|max:255|unique:administradores,Documento,' . $id,
            'Telefono' => 'nullable|string|max:255',
            'Email' => 'nullable|string|email|max:255|unique:administradores,Email,' . $id,
            'Password' => 'nullable|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->only(['Nombre', 'Apellido', 'Documento', 'Telefono', 'Email']);
        if ($request->has('Password')) {
            $data['Password'] = Hash::make($request->Password);
        }

        $administrador->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Administrador actualizado correctamente',
            'data' => $administrador,
        ], 200);
    }

    /**
     * Eliminar un administrador.
     */
    public function destroy($id)
    {
        $administrador = Administrador::find($id);
        if (!$administrador) {
            return response()->json([
                'success' => false,
                'message' => 'Administrador no encontrado',
            ], 404);
        }

        $administrador->delete();

        return response()->json([
            'success' => true,
            'message' => 'Administrador eliminado correctamente',
        ], 200);
    }
}