<?php

namespace App\Http\Controllers;
use App\models\Citas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class CitasController extends Controller
{
      public function index()
    {
        $citas = Citas::all();
        return response()->json($citas);
    }


   public function store(Request $request)
   {
        $validator = Validator::make($request->all(),[
        'Fecha_cita'=> 'required|date',
        'Hora'=> 'required',
        'Estado'=> 'required|string',
        'idPaciente'=>'required|integer',
        'idMedico'=>'required|integer',
        'idResepcionista'=>'required|integer',

        ]);

        if ($validator-> fails()) {
          return response()->json($validator->errors(), 422);
         }

        $citas = Citas::create($validator->validated());
        return response()->json($citas,201);  

   } 

   public function show(string $id)   
    {
        $citas = Citas::find($id);

        if (!$citas) { 
            return response->json(['menssage'=> 'Cita no encontrada'], 404);
        }

        return response()->json($citas);
    } 

  public function update(Request $request, string $id)  
    {
          $citas = Citas::find($id);

          if (!$citas) { 
            return response->json(['menssage'=> 'Cita no encontrada para editar '], 404);
        }

         $validator = Validator::make($request->all(),[
          'Fecha_cita'=> 'date',
          'Hora'=> 'time',
          'Estado'=> 'string',
          'idPaciente'=>'integer',
          'idMedico'=>'integer',
         'idRecepcionista'=>'integer',
        ]);

          if ($validator-> fails()) {
         return response()->json($validator->errors(), 422);
        }

        $citas->update($validator->validated());
        return response()->json($citas); 
    }

    public function destroy (string $id)
   {
         $citas = Citas::find($id);
          if (!$citas) { 
            return response->json(['menssage'=> 'Cita no encontrada para eliminar '], 404);
        }
          $citas->delete();
          return response()->json(['message' => 'Cita eliminada con exito']); 
    } 
 
}
