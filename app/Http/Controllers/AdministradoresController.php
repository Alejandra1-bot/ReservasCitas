<?php

namespace App\Http\Controllers;

use App\Models\Administrador;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AdministradoresController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $administradores = Administrador::all();
        return response()->json($administradores);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Nombre' => 'required|string',
            'Apellido' => 'required|string',
            'Documento' => 'required|string|unique:administradores',
            'Telefono' => 'required|string',
            'Email' => 'required|string|unique:administradores',
            'Password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Crear usuario en tabla users
        $user = User::create([
            'name' => $request->Nombre,
            'apellido' => $request->Apellido,
            'documento' => $request->Documento,
            'telefono' => $request->Telefono,
            'email' => $request->Email,
            'password' => Hash::make($request->Password),
            'rol' => 'administrador',
        ]);

        $administrador = Administrador::create([
            'Nombre' => $request->Nombre,
            'Apellido' => $request->Apellido,
            'Documento' => $request->Documento,
            'Telefono' => $request->Telefono,
            'Email' => $request->Email,
            'Password' => Hash::make($request->Password),
        ]);

        return response()->json($administrador, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $administrador = Administrador::find($id);

        if (!$administrador) {
            return response()->json(['message' => 'Administrador no encontrado'], 404);
        }

        return response()->json($administrador);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $administrador = Administrador::find($id);

        if (!$administrador) {
            return response()->json(['message' => 'Administrador no encontrado para editar'], 404);
        }

        $validator = Validator::make($request->all(), [
            'Nombre' => 'string',
            'Apellido' => 'string',
            'Documento' => 'string|unique:administradores,Documento,' . $id,
            'Telefono' => 'string',
            'Email' => 'string|unique:administradores,Email,' . $id,
            'Password' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $validator->validated();
        if (isset($data['Password'])) {
            $data['Password'] = Hash::make($data['Password']);
        }
        $administrador->update($data);
        return response()->json($administrador);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $administrador = Administrador::find($id);

        if (!$administrador) {
            return response()->json(['message' => 'Administrador no encontrado para eliminar'], 404);
        }

        $administrador->delete();
        return response()->json(['message' => 'Administrador eliminado con éxito']);
    }
}
