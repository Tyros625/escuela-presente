@php
    $student = $student ?? $data['student'] ?? null;
    $group = $group ?? $data['group'] ?? null;
    $grades = $grades ?? $data['grades'] ?? null;
    $schoolName = $schoolName ?? ($generalConfiguration->school_name ?? 'Escuela');
    $schoolLogo = $schoolLogo ?? ($generalConfiguration->logo ?? null);
    $schoolCycle = $schoolCycle ?? ($group['school_cycle'] ?? null);
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleta de Calificaciones</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .header { text-align: center; margin-bottom: 20px; }
        .logo { max-height: 60px; margin-bottom: 10px; }
        .section-title { font-weight: bold; margin-top: 10px; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #444; padding: 4px 6px; text-align: left; }
        .text-right { text-align: right; }
        .mt-20 { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        @if($schoolLogo)
            <img class="logo" src="{{ public_path($schoolLogo) }}" alt="Logo">
        @endif
        <div>{{ $schoolName }}</div>
        @if($schoolCycle)
            <div>Ciclo escolar: {{ $schoolCycle }}</div>
        @endif
        <div class="mt-20"><strong>Boleta de Calificaciones</strong></div>
    </div>

    @if($student)
        <div class="section-title">Datos del alumno</div>
        <table>
            <tr>
                <th>Nombre</th>
                <td>
                    {{ $student['last_name_father'] ?? '' }}
                    {{ $student['last_name_mother'] ?? '' }},
                    {{ $student['name'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>Matrícula</th>
                <td>{{ $student['enrollment'] ?? '' }}</td>
            </tr>
        </table>
    @endif

    @if($group)
        <div class="section-title">Grupo</div>
        <table>
            <tr>
                <th>Grado</th>
                <td>{{ $group['grade'] ?? '' }}</td>
            </tr>
            <tr>
                <th>Sección</th>
                <td>{{ $group['section'] ?? '' }}</td>
            </tr>
            <tr>
                <th>Grupo</th>
                <td>{{ $group['name'] ?? '' }}</td>
            </tr>
        </table>
    @endif

    @if($grades)
        <div class="section-title">Calificaciones</div>
        <table>
            <thead>
                <tr>
                    <th>Parcial 1</th>
                    <th>Parcial 2</th>
                    <th>Parcial 3</th>
                    <th>Promedio</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $grades['partial_1'] ?? '' }}</td>
                    <td>{{ $grades['partial_2'] ?? '' }}</td>
                    <td>{{ $grades['partial_3'] ?? '' }}</td>
                    <td>{{ $grades['average'] ?? '' }}</td>
                    <td>{{ ($grades['status'] ?? '') === 'approved' ? 'Aprobado' : 'Reprobado' }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    <div class="mt-20 text-right">
        Fecha de emisión: {{ now()->format('d/m/Y') }}
    </div>
</body>
</html>

