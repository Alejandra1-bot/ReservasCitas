<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recuperación de Contraseña</title>
  <style>
    body {
      font-family: 'Segoe UI', Roboto, Arial, sans-serif;
      background-color: #f4f6f8;
      margin: 0;
      padding: 0;
      color: #333;
    }

    .container {
      max-width: 600px;
      margin: 40px auto;
      background-color: #ffffff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      overflow: hidden;
    }

    .header {
      background: linear-gradient(135deg, #0077b6, #00b4d8);
      padding: 20px;
      text-align: center;
      color: #fff;
    }

    .header h1 {
      margin: 0;
      font-size: 22px;
      letter-spacing: 0.5px;
    }

    .content {
      padding: 30px;
    }

    .content p {
      line-height: 1.6;
      font-size: 15px;
      margin: 10px 0;
    }

    .code-box {
      text-align: center;
      margin: 25px 0;
    }

    .code {
      font-size: 28px;
      font-weight: bold;
      color: #0077b6;
      background-color: #e0f7fa;
      padding: 14px 24px;
      border-radius: 8px;
      display: inline-block;
      letter-spacing: 3px;
    }

    .footer {
      background-color: #f0f4f8;
      text-align: center;
      padding: 20px;
      font-size: 13px;
      color: #777;
    }

    .footer p {
      margin: 0;
    }

    @media (max-width: 600px) {
      .container {
        margin: 20px;
      }

      .content {
        padding: 20px;
      }

      .code {
        font-size: 24px;
        padding: 10px 18px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Recuperación de Contraseña</h1>
    </div>

    <div class="content">
      <p>Hola,</p>
      <p>Hemos recibido una solicitud para restablecer tu contraseña. Usa el siguiente código de verificación para completar el proceso:</p>

      <div class="code-box">
        <div class="code">{{ $token }}</div>
      </div>

      <p>Ingresa este código de 6 dígitos en la aplicación para restablecer tu contraseña.</p>
      <p>⚠️ Este código expirará en <strong>60 minutos</strong>.</p>
      <p>Si no solicitaste este cambio, simplemente ignora este mensaje.</p>

      <p>Atentamente,<br><strong>Equipo de MediCare</strong></p>
    </div>

    <div class="footer">
      <p>© 2025 MediCare. Todos los derechos reservados.</p>
    </div>
  </div>
</body>
</html>
