<?php  

namespace App\Http\Controllers;  

use App\Models\Citas;  
use Illuminate\Http\Request;  
use Illuminate\Support\Facades\Validator;  
use Tymon\JWTAuth\Facades\JWTAuth;  

class CitasController extends Controller  
{  
    // Listar citas (según rol)
    public function index()
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();
            $userId = $payload->get('sub');
            $role = $payload->get('role');

            if ($role === 'paciente') {
                $user = \App\Models\User::find($userId);
                if (!$user) {
                    return response()->json(['error' => 'Usuario no encontrado'], 404);
                }

                $paciente = \App\Models\Pacientes::where('Email', $user->email)->first();
                if (!$paciente) {
                    return response()->json(['error' => 'Paciente no encontrado'], 404);
                }

                $citas = Citas::where('idPaciente', $paciente->id)->get();

            } elseif ($role === 'medico') {
                $user = \App\Models\User::find($userId);
                if (!$user) {
                    return response()->json(['error' => 'Usuario no encontrado'], 404);
                }

                $medico = \App\Models\Medicos::where('Email', $user->email)->first();
                if (!$medico) {
                    return response()->json(['error' => 'Médico no encontrado'], 404);
                }

                $citas = Citas::where('idMedico', $medico->id)->get();

            } else {
                // Admin o recepcionista ven todo
                $citas = Citas::all();
            }

            return response()->json($citas);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Token inválido', 'detalle' => $e->getMessage()], 401);
        }
    }

    // Crear cita
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'Fecha_cita'     => 'required|date',
            'Hora'           => 'required|string',
            'Estado'         => 'required|in:pendiente,Confirmada,cancelada',
            'idPaciente'     => 'required|integer|exists:pacientes,id',
            'idMedico'       => 'required|integer|exists:medicos,id',
            'idResepcionista'=> 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $cita = Citas::create($validator->validated());
        return response()->json($cita, 201);
    }

    // Mostrar una cita
    public function show(string $id)
    {
        $cita = Citas::find($id);

        if (!$cita) {
            return response()->json(['message'=> 'Cita no encontrada'], 404);
        }

        return response()->json($cita);
    }

    // Editar cita
    public function update(Request $request, string $id)
    {
        $cita = Citas::find($id);

        if (!$cita) {
            return response()->json(['message'=> 'Cita no encontrada para editar'], 404);
        }

        $validator = Validator::make($request->all(),[
            'Fecha_cita'     => 'date',
            'Hora'           => 'string',
            'Estado'         => 'in:pendiente,confirmada,cancelada',
            'idPaciente'     => 'integer|exists:pacientes,id',
            'idMedico'       => 'integer|exists:medicos,id',
            'idResepcionista'=> 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $cita->update($validator->validated());
        return response()->json($cita);
    }

    // Eliminar cita
    public function destroy(string $id)
    {
        $cita = Citas::find($id);

        if (!$cita) {
            return response()->json(['message'=> 'Cita no encontrada para eliminar'], 404);
        }

        $cita->delete();
        return response()->json(['message' => 'Cita eliminada con éxito']);
    }
}
