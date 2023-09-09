<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <p>
        Hola {{ $input['school_name'] }}, gracias por preferir nuestro servicio.
    </p>
    <p>
        Su usuario se a creado correctamente. Puede ingresar a el a través del siguiente <b><a
                href="{{ 'https://' . $input['domain'] }}">link</a></b>.
    </p>
</body>

</html>
