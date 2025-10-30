<?php  

namespace App\Http\Controllers;  

use App\Models\Citas;
use App\Models\Pacientes;
use App\Models\Medicos;
use App\Models\Resepcionistas;
use App\Mail\CitaConfirmacionMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
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

        // Enviar email de confirmación al paciente
        try {
            $paciente = Pacientes::find($request->idPaciente);
            $medico = Medicos::find($request->idMedico);

            if ($paciente && $paciente->Email) {
                $recepcionista = null;
                if ($request->idResepcionista) {
                    $recepcionista = Resepcionistas::find($request->idResepcionista);
                }

                $citaData = [
                    'paciente' => $paciente->Nombre . ' ' . $paciente->Apellido,
                    'fecha' => $request->Fecha_cita,
                    'hora' => $request->Hora,
                    'medico' => $medico ? $medico->Nombre . ' ' . $medico->Apellido : 'No asignado',
                    'especialidad' => $medico && $medico->especialidad ? $medico->especialidad->Nombre : 'General',
                    'estado' => $request->Estado,
                    'recepcionista' => $recepcionista ? $recepcionista->Nombre . ' ' . $recepcionista->Apellido : null,
                    'consultorio' => $medico && $medico->consultorio ? $medico->consultorio->Nombre : null,
                ];

                Mail::to($paciente->Email)->send(new CitaConfirmacionMail($citaData));
            }
        } catch (\Exception $e) {
            // Log error but don't fail the cita creation
            \Log::error('Error sending cita confirmation email: ' . $e->getMessage());
        }

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

        // Guardar valores anteriores para comparar
        $valoresAnteriores = [
            'Fecha_cita' => $cita->Fecha_cita,
            'Hora' => $cita->Hora,
            'Estado' => $cita->Estado,
            'idMedico' => $cita->idMedico,
            'idResepcionista' => $cita->idResepcionista,
        ];

        $cita->update($validator->validated());

        // Verificar si cambió algún campo importante
        $cambioDetectado = false;
        $tipoCambio = '';

        if ($valoresAnteriores['Estado'] !== $cita->Estado) {
            $cambioDetectado = true;
            $tipoCambio = 'cambio_estado';
        } elseif ($valoresAnteriores['Fecha_cita'] !== $cita->Fecha_cita ||
                  $valoresAnteriores['Hora'] !== $cita->Hora ||
                  $valoresAnteriores['idMedico'] !== $cita->idMedico ||
                  $valoresAnteriores['idResepcionista'] !== $cita->idResepcionista) {
            $cambioDetectado = true;
            $tipoCambio = 'cambio_detalles';
        }

        // Enviar email si hubo cambios
        if ($cambioDetectado) {
            try {
                $paciente = Pacientes::find($cita->idPaciente);
                $medico = Medicos::find($cita->idMedico);

                if ($paciente && $paciente->Email) {
                    $recepcionista = null;
                    if ($cita->idResepcionista) {
                        $recepcionista = Resepcionistas::find($cita->idResepcionista);
                    }

                    // Obtener datos del médico anterior si cambió
                    $medicoAnterior = null;
                    if ($valoresAnteriores['idMedico'] !== $cita->idMedico) {
                        $medicoAnterior = \App\Models\Medicos::find($valoresAnteriores['idMedico']);
                    }
    
                    // Obtener datos del recepcionista anterior si cambió
                    $recepcionistaAnterior = null;
                    if ($valoresAnteriores['idResepcionista'] !== $cita->idResepcionista && $valoresAnteriores['idResepcionista']) {
                        $recepcionistaAnterior = \App\Models\Resepcionistas::find($valoresAnteriores['idResepcionista']);
                    }
    
                    $citaData = [
                        'tipo' => $tipoCambio,
                        'paciente' => $paciente->Nombre . ' ' . $paciente->Apellido,
                        'fecha_anterior' => $valoresAnteriores['Fecha_cita'] !== $cita->Fecha_cita ? $valoresAnteriores['Fecha_cita'] : null,
                        'fecha_nueva' => $cita->Fecha_cita,
                        'hora_anterior' => $valoresAnteriores['Hora'] !== $cita->Hora ? $valoresAnteriores['Hora'] : null,
                        'hora_nueva' => $cita->Hora,
                        'medico_anterior' => $medicoAnterior ? $medicoAnterior->Nombre . ' ' . $medicoAnterior->Apellido : null,
                        'medico_nuevo' => $medico ? $medico->Nombre . ' ' . $medico->Apellido : 'No asignado',
                        'especialidad' => $medico && $medico->especialidad ? $medico->especialidad->Nombre : 'General',
                        'estado_anterior' => $tipoCambio === 'cambio_estado' ? $valoresAnteriores['Estado'] : null,
                        'estado_nuevo' => $tipoCambio === 'cambio_estado' ? $cita->Estado : $cita->Estado,
                        'recepcionista_anterior' => $recepcionistaAnterior ? $recepcionistaAnterior->Nombre . ' ' . $recepcionistaAnterior->Apellido : null,
                        'recepcionista_nuevo' => $recepcionista ? $recepcionista->Nombre . ' ' . $recepcionista->Apellido : null,
                        'consultorio' => $medico && $medico->consultorio ? $medico->consultorio->Nombre : null,
                    ];

                    Mail::to($paciente->Email)->send(new CitaConfirmacionMail($citaData));
                }
            } catch (\Exception $e) {
                // Log error but don't fail the cita update
                \Log::error('Error sending cita status change email: ' . $e->getMessage());
            }
        }

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
