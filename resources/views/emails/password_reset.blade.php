<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecimiento de Contraseña - LANCETA HG</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #0056b3;
            text-align: center;
        }
        p {
            font-size: 16px;
            line-height: 1.6;
            color: #333;
        }
        .button {
            display: block;
            width: 100%;
            max-width: 200px;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #00B398; /* Color azul más claro */
            color: #fff;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
        }
        .footer {
            font-size: 12px;
            color: #888;
            text-align: center;
            margin-top: 30px;
        }

        
    </style>
</head>
<body>
    <div class="container">
        <!-- Logo de la Empresa -->
        <div class="logo">
            <h1>LANCETA HG</h1>
        </div>

        <!-- Contenido del Correo -->
        <p>Hola {{ $user->name }},</p>
        <p>Hemos recibido una solicitud para restablecer la contraseña de su cuenta. Haz clic en el botón de abajo para establecer una nueva contraseña:</p>
        
        <a href="{{ url('/reset-password/' . $token) }}" class="button">Restablecer Contraseña</a>

        <p>Si no solicitaste este restablecimiento, ignora este correo. Su contraseña no será modificada hasta que accedas al enlace y crees una nueva.</p>

        <!-- Aviso de Correo Automático -->
        <div class="footer">
            <p>Este es un correo generado automáticamente por el sistema de <strong>LANCETA HG</strong>. Por favor, no responda a este correo.</p>
        </div>
    </div>
</body>
</html>
