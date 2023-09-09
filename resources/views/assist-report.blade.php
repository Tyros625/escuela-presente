<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reporte</title>
</head>

<body>
    <table style="border-collapse: collapse; width: 100%;" border="0">
        <tbody>
            <tr>
                <td style="width: 20%; text-align: center;">
                    <img style="max-width: 100%" src="{{$logo}}" />
                </td>
                <td style="width: 80%;">
                    <p><b>Escuela:</b> {{$name}}</p>
                    <p><b>CCT:</b> {{$cct}}</p>
                    <p><b>Dirección:</b> {{$address}}</p>
                </td>
            </tr>
        </tbody>
    </table>
    <h2 style="text-align: center;"><b>Reporte de Asistencia</b></h2>
    <p style="text-align: center;"><b>Fecha:</b> {{$date}}</p>
    <p style="text-align: center;">
        <img style="max-width: 100%" src="{{$url}}">
    </p>
    <table style="border-collapse: collapse; width: 70%; height: 90px; margin-left: auto; margin-right: auto;" border="1">
        <tbody>
            <tr style="height: 18px;">
                <td style="width: 50%; height: 18px;">&nbsp;</td>
                <td style="width: 50%; height: 18px; text-align: center;"><b>{{$grade}}</b></td>
            </tr>
            @php
                $sum = 0;
            @endphp
            @foreach ($dataset as $key => $item)
            <tr style="height: 18px;">
                <td style="width: 50%; text-align: center; height: 18px;">Grupo {{$key}}</td>
                <td style="width: 50%; height: 18px; text-align: center;"><b>{{$item}}</b></td>
            </tr>
            @php
                $sum += $item;
            @endphp
            @endforeach
            <tr style="height: 18px;">
                <td style="width: 50%; text-align: center; height: 18px;">Total de Alumnos</td>
                <td style="width: 50%; height: 18px; text-align: center;"><b>{{$sum}}</b></td>
            </tr>
        </tbody>
    </table>
</body>

</html>
