<?php

namespace App\Http\Controllers;
use App\models\Medicos;
use App\Models\Pacientes;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class MedicosController extends Controller
{
     public function index()
    {
        $medicos = Medicos::all();
        return response()->json($medicos);
    }


   public function store(Request $request)
   {
        $validator = Validator::make($request->all(),[
        'Nombre'=> 'required|string',
        'Apellido'=> 'required|string',
        'Documento'=> 'required|string',
        'Telefono'=> 'required|string',
        'Email'=> 'required|string',
        'Password'=> 'required|string',
        'Fecha_nacimiento'=> 'nullable',
        'Genero'=> 'nullable',
        'RH'=> 'nullable',
        'Nacionalidad'=> 'nullable',
        'idConsultorio'=>'required|integer',
        'idEspecialidad'=>'required|integer'

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
             'fechaNacimiento' => $request->Fecha_nacimiento,
             'genero' => $request->Genero,
             'rh' => $request->RH,
             'nacionalidad' => $request->Nacionalidad,
             'password' => Hash::make($request->Password),
             'rol' => 'medico',
         ]);

         // Crear médico
         $medicos = Medicos::create([
             'Nombre' => $request->Nombre,
             'Apellido' => $request->Apellido,
             'Documento' => $request->Documento,
             'Telefono' => $request->Telefono,
             'Email' => $request->Email,
             'Password' => Hash::make($request->Password),
             'idConsultorio' => $request->idConsultorio,
             'idEspecialidad' => $request->idEspecialidad
         ]);

         return response()->json(['medico' => $medicos], 201);

   } 

   public function show(string $id)   
    {
        $medicos = Medicos::find($id);

        if (!$medicos) { 
            return response->json(['menssage'=> 'Medico no encontrado'], 404);
        }

        return response()->json($medicos);
    } 

  public function update(Request $request, string $id)  
    {
          $medicos = Medicos::find($id);

          if (!$medicos) { 
            return response->json(['menssage'=> 'Medicos no encontrado para editar '], 404);
        }

         $validator = Validator::make($request->all(),[
        'Nombre'=> 'string',
        'Apellido'=> 'string',
        'Documento'=> 'string',
        'Telefono'=> 'string',
        'Email'=> 'string',
        'Password'=> 'string',
        'idConsultorio'=>'string',
        'idEspecialidad'=>'string'



        ]);

          if ($validator->fails()) {
         return response()->json($validator->errors(), 422);
        }

        $data = $validator->validated();
        if (isset($data['Password'])) {
            $data['Password'] = Hash::make($data['Password']);
        }
        $medicos->update($data);
        return response()->json($medicos);
    }

    public function destroy (string $id)
   {
         $medicos = Medicos::find($id);
          if (!$medicos) { 
            return response->json(['menssage'=> 'Medico no encontrado para eliminar '], 404);
        }
          $medicos->delete();
          return response()->json(['message' => 'Medico eliminado con exito']); 
    } 
 
}
