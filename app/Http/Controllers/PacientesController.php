<?php

namespace App\Http\Controllers;
use App\Models\Pacientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        'Nombre'=> 'required|string',
        'Apellido'=> 'required|string',
        'Documento'=> 'required|string',
        'Telefono'=> 'required|string',
        'Email'=> 'required|string',
        'Fecha_nacimiento'=> 'required|date',
        'Genero'=> 'required|string',
        'RH'=> 'required|string',
        'Nacionalidad'=> 'required|string',
        'Password'=> 'required|string',
        ]);

        if ($validator-> fails()) {
          return response()->json($validator->errors(), 422);
         }

        $pacientes = Pacientes::create($validator->validated());
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
        'Nombre'=> 'string',
        'Apellido'=> 'string',
        'Documento'=> 'string',
        'Telefono'=> 'string',
        'Email'=> 'string',
        'Fecha_nacimiento'=> 'date',
        'Genero'=> 'string',
        'RH'=> 'string',
        'Nacionalidad'=> 'string',
        'Password'=> 'string',
        ]);

          if ($validator-> fails()) {
         return response()->json($validator->errors(), 422);
        }

        $pacientes->update($validator->validated());
        return response()->json($pacientes); 
    }

    public function destroy (string $id)
   {
         $pacientes = Pacientes::find($id);
          if (!$pacientes) { 
            return response->json(['menssage'=> 'Paciente no encontrado para eliminar '], 404);
        }
          $pacientes->delete();
          return response()->json(['message' => 'Paciente eliminado con exito']); 
    } 
 
}
