<!DOCTYPE html>
<html>
<head>
    <title>Verifica tu correo electrónico</title>
</head>
<body>
    <p>Hola {{ $user->name }},</p>
    <p>Por favor, haz clic en el siguiente enlace para verificar tu correo electrónico:</p>
    <p><a href="{{ $verificationLink }}">Verificar correo electrónico</a></p>
    <p>Si no has creado una cuenta, puedes ignorar este correo.</p>
</body>
</html>
