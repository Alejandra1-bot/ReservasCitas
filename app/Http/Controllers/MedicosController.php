<?php

namespace App\Http\Controllers;
use App\models\Medicos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        'idConsultorio'=>'required|string',
        'idEspecialidad'=>'required|string'

        ]);

        if ($validator-> fails()) {
          return response()->json($validator->errors(), 422);
         }

        $medicos = Medicos::create($validator->validated());
        return response()->json($medicos,201);  

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

          if ($validator-> fails()) {
         return response()->json($validator->errors(), 422);
        }

        $medicos->update($validator->validated());
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
