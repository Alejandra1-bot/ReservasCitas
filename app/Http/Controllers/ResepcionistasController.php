<?php

namespace App\Http\Controllers;
use App\Models\Resepcionistas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ResepcionistasController extends Controller
{
      public function index()
    {
        $resepcionistas = Resepcionistas::all();
        return response()->json($resepcionistas);
    }

   public function store(Request $request)
   {
        $validator = Validator::make($request->all(),[
        'Nombre'=> 'required|string',
        'Apellido'=> 'required|string',
        'Turno'=> 'required|string',
        'Telefono'=> 'required|string',
        'Email'=> 'required|string',
        'Password'=> 'required|string',

    
        ]);

        if ($validator-> fails()) {
          return response()->json($validator->errors(), 422);
         }

        $data = $validator->validated();
        if (isset($data['Password'])) {
            $data['Password'] = Hash::make($data['Password']);
        }

        $resepcionistas = Resepcionistas::create($data);
        return response()->json($resepcionistas,201);

   }

   public function show(string $id)   
    {
        $resepcionistas = Resepcionistas::find($id);

        if (!$resepcionistas) { 
            return response->json(['menssage'=> 'Resepcionista no encontrada'], 404);
        }

        return response()->json($resepcionistas);
    } 

   public function update(Request $request, string $id)  
    {
          $resepcionistas = Resepcionistas::find($id);

          if (!$resepcionistas) { 
            return response->json(['menssage'=> 'Resepcionista no encontrado para editar '], 404);
        }

         $validator = Validator::make($request->all(),[
          'Nombre'=> 'string',
          'Apellido'=> 'string',
          'Turno'=> 'string',
          'Telefono'=> 'string',
          'Email'=> 'string',
          'Password'=> 'string',

        ]);

          if ($validator-> fails()) {
         return response()->json($validator->errors(), 422);
        }

        $data = $validator->validated();
        if (isset($data['Password'])) {
            $data['Password'] = Hash::make($data['Password']);
        }

        $resepcionistas->update($data);
        return response()->json($resepcionistas); 
    }

    public function destroy (string $id)
   {
         $resepcionistas = Resepcionistas::find($id);
          if (!$resepcionistas) { 
            return response->json(['menssage'=> 'Resepcionista no encontrado para eliminar '], 404);
        }
          $resepcionistas->delete();
          return response()->json(['message' => 'Resepcionista eliminado con exito']); 
    } 
}
