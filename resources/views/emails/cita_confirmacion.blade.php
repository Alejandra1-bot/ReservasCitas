<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Confirmación de Cita Médica</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border: 1px solid #dee2e6;
        }
        .cita-info {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #007bff;
        }
        .footer {
            background-color: #6c757d;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 0 0 5px 5px;
            font-size: 12px;
        }
        .important {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>
            @if(isset($citaData['tipo']))
                @if($citaData['tipo'] === 'cambio_estado')
                    Actualización de Estado de Cita Médica
                @elseif($citaData['tipo'] === 'cambio_detalles')
                    Actualización de Detalles de Cita Médica
                @endif
            @else
                Confirmación de Cita Médica
            @endif
        </h1>
        <p>MediCare</p>
    </div>

    <div class="content">
        <p>Hola <strong>{{ $citaData['paciente'] }}</strong>,</p>

        @if(isset($citaData['tipo']))
            @if($citaData['tipo'] === 'cambio_estado')
                <p>Te informamos que el estado de tu cita médica ha cambiado. A continuación encontrarás los detalles actualizados:</p>
            @elseif($citaData['tipo'] === 'cambio_detalles')
                <p>Te informamos que algunos detalles de tu cita médica han sido modificados. A continuación encontrarás la información actualizada:</p>
            @endif
        @else
            <p>Tu cita médica ha sido programada exitosamente. A continuación encontrarás los detalles de tu cita:</p>
        @endif

        <div class="cita-info">
            <h3>@if(isset($citaData['tipo'])) Detalles Actualizados de la Cita @else Detalles de la Cita @endif</h3>

            @if(isset($citaData['tipo']) && $citaData['tipo'] === 'cambio_detalles')
                @if($citaData['fecha_anterior'])
                    <p><strong>Fecha anterior:</strong> {{ \Carbon\Carbon::parse($citaData['fecha_anterior'])->format('d/m/Y') }}</p>
                    <p><strong>Fecha nueva:</strong> <span class="important">{{ \Carbon\Carbon::parse($citaData['fecha_nueva'])->format('d/m/Y') }}</span></p>
                @else
                    <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($citaData['fecha_nueva'])->format('d/m/Y') }}</p>
                @endif

                @if($citaData['hora_anterior'])
                    <p><strong>Hora anterior:</strong> {{ $citaData['hora_anterior'] }}</p>
                    <p><strong>Hora nueva:</strong> <span class="important">{{ $citaData['hora_nueva'] }}</span></p>
                @else
                    <p><strong>Hora:</strong> {{ $citaData['hora_nueva'] }}</p>
                @endif

                @if($citaData['medico_anterior'])
                    <p><strong>Médico anterior:</strong> {{ $citaData['medico_anterior'] }}</p>
                    <p><strong>Médico nuevo:</strong> <span class="important">{{ $citaData['medico_nuevo'] }}</span></p>
                @else
                    <p><strong>Médico:</strong> {{ $citaData['medico_nuevo'] }}</p>
                @endif

                @if($citaData['recepcionista_anterior'])
                    <p><strong>Recepcionista anterior:</strong> {{ $citaData['recepcionista_anterior'] }}</p>
                    <p><strong>Recepcionista nuevo:</strong> <span class="important">{{ $citaData['recepcionista_nuevo'] ?: 'No asignado' }}</span></p>
                @elseif($citaData['recepcionista_nuevo'])
                    <p><strong>Recepcionista:</strong> {{ $citaData['recepcionista_nuevo'] }}</p>
                @endif
            @else
                <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($citaData['fecha_nueva'] ?? $citaData['fecha'])->format('d/m/Y') }}</p>
                <p><strong>Hora:</strong> {{ $citaData['hora_nueva'] ?? $citaData['hora'] }}</p>
                <p><strong>Médico:</strong> {{ $citaData['medico_nuevo'] ?? $citaData['medico'] }}</p>

                @if(isset($citaData['recepcionista_nuevo']))
                    <p><strong>Recepcionista:</strong> {{ $citaData['recepcionista_nuevo'] }}</p>
                @elseif(isset($citaData['recepcionista']))
                    <p><strong>Recepcionista:</strong> {{ $citaData['recepcionista'] }}</p>
                @endif
            @endif

            <p><strong>Especialidad:</strong> {{ $citaData['especialidad'] ?? 'General' }}</p>

            @if(isset($citaData['tipo']) && $citaData['tipo'] === 'cambio_estado')
                <p><strong>Estado anterior:</strong> {{ $citaData['estado_anterior'] }}</p>
                <p><strong>Estado nuevo:</strong> <span class="important">{{ $citaData['estado_nuevo'] }}</span></p>
            @else
                <p><strong>Estado:</strong> <span class="important">{{ $citaData['estado_nuevo'] ?? $citaData['estado'] }}</span></p>
            @endif

            @if(isset($citaData['consultorio']))
            <p><strong>Consultorio:</strong> {{ $citaData['consultorio'] }}</p>
            @endif
        </div>

        <div class="cita-info">
            <h4>📋 Información Importante</h4>
            <ul>
                <li>Llega 15 minutos antes de tu cita</li>
                <li>Trae tu documento de identidad</li>
                <li>Si necesitas cancelar o reprogramar, háznoslo saber con anticipación</li>
                <li>Para cualquier cambio, contacta a recepción</li>
                  <li> Direccion : calle 50# 11 f 9</li>
            </ul>
        </div>

        <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>

        <p>¡Te esperamos!</p>
    </div>

    <div class="footer">
        <p>Este es un email automático, por favor no respondas a este mensaje.</p>
        <p>&copy; 2025 MediCare - Todos los derechos reservados</p>
    </div>
</body>
</html>