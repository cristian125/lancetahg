<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Modo Mantenimiento</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #00B398;
            text-align: center;
            padding: 50px;
        }

        .container-xl {
            border-radius: 10px;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
            /* Sombra más suave */
            max-width: 1200px;
            margin: 0 auto;
            overflow: hidden;
        }

        .bg-white {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
        }

        .bg-lanceta1 {
            background-color: #03587C;
            padding: 20px;
        }

        h1 {
            color: #333;
            font-size: 28px;
        }

        p {
            color: #666;
            font-size: 18px;
        }

        .image {
            margin: 20px 0;
        }

        img {
            max-width: 50%;
            height: auto;
            border-radius: 10px;
            /* Borde redondeado para el GIF */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            /* Sombra alrededor del GIF */
            border: 3px solid #03587C;
            /* Borde del mismo color que el fondo */
        }
    </style>
</head>

<body>


    <div class="container-xl bg-lanceta1">
        <img src="{{ asset('storage/logos/logolhg.png') }}" alt="Sitio en Construcción">
        <div class="bg-white">
            <h1>Sitio en Mantenimiento</h1>
            <p>Estamos trabajando en mejoras. Estaremos de vuelta pronto.</p>

            <!-- Imagen de construcción -->
            <div class="image">
                <img src="{{ asset('storage/img/mantenimiento.gif') }}" alt="Sitio en Construcción">
            </div>

            <p>Gracias por su paciencia.</p>
            <br />
            <br />
        </div>
    </div>
</body>

</html>
