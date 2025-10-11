<?php

namespace App\Http\Controllers;
use App\Models\Pacientes;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class PacientesController extends Controller
{
    public function index()
    {
        $pacientes = Pacientes::all();
        return response()->json($pacientes);
    }
    

   public function store(Request $request)
          {

        $validator = Validator::make($request->all(),[
        'name'=> 'required|string',
        'Apellido'=> 'required|string',
        'Documento'=> 'required|string',
        'Telefono'=> 'required|string',
        'Email'=> 'required|string',
        'Fecha_nacimiento'=> 'required|date',
        'Genero'=> 'required|string',
        'RH'=> 'required|string',
        'Nacionalidad'=> 'required|string',
        'password'=> 'required|string',
        ]);

        if ($validator->fails()) {
           return response()->json($validator->errors(), 422);
         }

         $data = $validator->validated();
         $data['password'] = Hash::make($data['password']);

         // Crear usuario en tabla users
         $user = User::create([
             'name' => $data['Nombre'],
             'apellido' => $data['Apellido'],
             'documento' => $data['Documento'],
             'telefono' => $data['Telefono'],
             'email' => $data['Email'],
             'fechaNacimiento' => $data['Fecha_nacimiento'],
             'genero' => $data['Genero'],
             'rh' => $data['RH'],
             'nacionalidad' => $data['Nacionalidad'],
             'password' => $data['password'],
             'rol' => 'paciente',
         ]);

         $pacientes = Pacientes::create($data);
         return response()->json($pacientes,201);

   } 

    public function show(string $id)   
    {
        $pacientes = Pacientes::find($id);

        if (!$pacientes) { 
            return response->json(['menssage'=> 'Paciente no encontrado'], 404);
        }

        return response()->json($pacientes);
    } 

  public function update(Request $request, string $id)  
    {
          $pacientes = Pacientes::find($id);

          if (!$pacientes) { 
            return response->json(['menssage'=> 'Paciente no encontrado para editar '], 404);
        }

         $validator = Validator::make($request->all(),[
        'name'=> 'string',
        'Apellido'=> 'string',
        'Documento'=> 'string',
        'Telefono'=> 'string',
        'Email'=> 'string',
        'Fecha_nacimiento'=> 'date',
        'Genero'=> 'string',
        'RH'=> 'string',
        'Nacionalidad'=> 'string',
        'password'=> 'string',
        ]);

          if ($validator->fails()) {
         return response()->json($validator->errors(), 422);
        }

        $data = $validator->validated();
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $pacientes->update($data);
        return response()->json($pacientes);
    }

    public function destroy (string $id)
   {
         $pacientes = Pacientes::find($id);
          if (!$pacientes) { 
            return response()->json(['menssage'=> 'Paciente no encontrado para eliminar '], 404);
        }
          $pacientes->delete();
          return response()->json(['message' => 'Paciente eliminado con exito']); 
    } 
 
}
