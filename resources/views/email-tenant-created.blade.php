<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cuenta Creada - Escuela Presente</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 10px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #0369a1;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .button:hover {
            background-color: #0284c7;
        }
        .domain-link {
            font-size: 18px;
            font-weight: bold;
            color: #0369a1;
            word-break: break-all;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>¡Bienvenido a Escuela Presente!</h2>
        
        <p>
            Hola <strong>{{ $input['school_name'] }}</strong>, gracias por preferir nuestro servicio.
        </p>
        
        <p>
            Su cuenta ha sido creada correctamente. Su dominio personalizado es:
        </p>
        
        <p class="domain-link">
            {{ $input['domain'] }}
        </p>
        
        <p>
            Puede acceder a su sistema haciendo clic en el siguiente botón:
        </p>
        
        <p style="text-align: center;">
            <a href="{{ 'https://' . $input['domain'] }}" class="button">
                Acceder a mi Sistema
            </a>
        </p>
        
        <p>
            O copie y pegue el siguiente enlace en su navegador:
        </p>
        
        <p style="word-break: break-all; color: #0369a1;">
            https://{{ $input['domain'] }}
        </p>
        
        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">
        
        <p style="font-size: 12px; color: #666;">
            <strong>Credenciales de acceso:</strong><br>
            Email: {{ $input['email'] }}<br>
            (Su contraseña es la que ingresó durante el registro)
        </p>
        
        <p style="font-size: 12px; color: #666; margin-top: 20px;">
            Si tiene alguna pregunta, no dude en contactarnos.<br>
            Saludos cordiales,<br>
            <strong>Equipo Escuela Presente</strong>
        </p>
    </div>
</body>

</html>
