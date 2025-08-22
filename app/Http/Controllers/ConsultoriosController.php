<?php

namespace App\Http\Controllers;
use App\models\Consultorios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ConsultoriosController extends Controller
{
    public function index()
    {
        $consultorios = Consultorios::all();
        return response()->json($consultorios);
    }

   public function store(Request $request)
   {
        $validator = Validator::make($request->all(),[
        'Nombre'=> 'required|string',
        'Direccion'=> 'required|string',
        'Ciudad'=> 'required|string',
        'Telefono'=> 'required|string',
    
        ]);

        if ($validator-> fails()) {
          return response()->json($validator->errors(), 422);
         }

        $consultorios = Consultorios::create($validator->validated());
        return response()->json($consultorios,201);  

   } 

   public function show(string $id)   
    {
        $consultorios = Consultorios::find($id);

        if (!$consultorios) { 
            return response->json(['menssage'=> 'Consultorio no encontrado'], 404);
        }

        return response()->json($consultorios);
    } 

  public function update(Request $request, string $id)  
    {
          $consultorios = Consultorios::find($id);

          if (!$consultorios) { 
            return response->json(['menssage'=> 'Consultorio no encontrado para editar '], 404);
        }

         $validator = Validator::make($request->all(),[
        'Nombre'=> 'string',
        'Direccion'=> 'string',
        'Ciudad'=> 'string',
        'Telefono'=> 'string',
        ]);

          if ($validator-> fails()) {
         return response()->json($validator->errors(), 422);
        }

        $consultorios->update($validator->validated());
        return response()->json($consultorios); 
    }

    public function destroy (string $id)
   {
         $consultorios = Consultorios::find($id);
          if (!$consultorios) { 
            return response->json(['menssage'=> 'Consultorio no encontrado para eliminar '], 404);
        }
          $consultorios->delete();
          return response()->json(['message' => 'Consultorio eliminado con exito']); 
    } 
 
}
