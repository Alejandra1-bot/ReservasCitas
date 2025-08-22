<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Especialidades;
use Illuminate\Support\Facades\Validator;


class EspecialidadesController extends Controller
{
      public function index()
    {
        $especialidades = Especialidades::all();
        return response()->json($especialidades);
    }

   public function store(Request $request)
   {
        $validator = Validator::make($request->all(),[
        'Nombre'=> 'required|string',
        'Descripcion'=> 'required|string',
    
        ]);

        if ($validator-> fails()) {
          return response()->json($validator->errors(), 422);
         }

        $especialidades = Especialidades::create($validator->validated());
        return response()->json($especialidades,201);  

   } 

   public function show(string $id)   
    {
        $especialidades = Especialidades::find($id);

        if (!$especialidades) { 
            return response->json(['menssage'=> 'Especialidad no encontrado'], 404);
        }

        return response()->json($especialidades);
    } 

  public function update(Request $request, string $id)  
    {
          $especialidades = Especialidades::find($id);

          if (!$especialidades) { 
            return response->json(['menssage'=> 'especialidad no encontrado para editar '], 404);
        }

         $validator = Validator::make($request->all(),[
          'Nombre'=> 'string',
          'Descripcion'=> 'string', 
        ]);

          if ($validator-> fails()) {
         return response()->json($validator->errors(), 422);
        }

        $especialidades->update($validator->validated());
        return response()->json($especialidades); 
    }

    public function destroy (string $id)
   {
         $especialidades = Especialidades::find($id);
          if (!$especialidades) { 
            return response->json(['menssage'=> 'especialidad no encontrado para eliminar '], 404);
        }
          $especialidades->delete();
          return response()->json(['message' => 'especialidad eliminado con exito']); 
    } 
}
