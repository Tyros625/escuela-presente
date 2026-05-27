<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Horario de docentes</title>
    <style>
        @page {
            size: legal landscape;
            margin: 8mm;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 7px;
            color: #111;
            margin: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 6px;
        }
        .header h1 {
            font-size: 11px;
            margin: 0 0 2px;
        }
        .header p {
            margin: 0;
            font-size: 8px;
        }
        table.grid {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        table.grid th,
        table.grid td {
            border: 1px solid #333;
            padding: 2px 1px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }
        table.grid thead th {
            background: #e8e8e8;
            font-weight: bold;
            font-size: 6px;
        }
        .col-num { width: 14px; }
        .col-teacher { width: 130px; text-align: left !important; font-size: 7px; padding-left: 3px !important; }
        .col-subject { width: 72px; text-align: left !important; font-size: 7px; padding-left: 3px !important; }
        .slot-head { font-size: 5px; line-height: 1.1; }
        .cell-filled {
            font-weight: bold;
            font-size: 6px;
            line-height: 1.15;
        }
        .empty-msg {
            text-align: center;
            padding: 24px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $school_name ?? 'Escuela' }} — Horario de docentes</h1>
        <p>
            Turno: {{ ($shift ?? 'morning') === 'afternoon' ? 'Vespertino' : 'Matutino' }}
            · Generado: {{ $generated_at ?? '' }}
        </p>
    </div>

    @php
        $slotsPerDay = (int) ($slots_per_day ?? count($slots ?? []));
        $totalDayCols = count($days ?? []) * max(1, $slotsPerDay);
    @endphp

    @if(empty($teachers))
        <p class="empty-msg">No hay asignaciones activas para generar el horario.</p>
    @else
        <table class="grid">
            <thead>
                <tr>
                    <th class="col-num" rowspan="2">#</th>
                    <th class="col-teacher" rowspan="2">DOCENTE</th>
                    <th class="col-subject" rowspan="2">MATERIA</th>
                    @foreach($day_labels ?? [] as $label)
                        <th colspan="{{ $slotsPerDay }}">{{ $label }}</th>
                    @endforeach
                </tr>
                <tr>
                    @foreach($days ?? [] as $day)
                        @foreach($slots ?? [] as $slot)
                            <th class="slot-head">{{ $slot }}</th>
                        @endforeach
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($teachers as $row)
                    <tr>
                        <td class="col-num">{{ $row['number'] }}</td>
                        <td class="col-teacher">{{ $row['teacher_name'] }}</td>
                        <td class="col-subject">{{ $row['subject_name'] }}</td>
                        @foreach($days ?? [] as $day)
                            @foreach($slots ?? [] as $slot)
                                @php
                                    $cell = $row['cells'][$day][$slot] ?? null;
                                @endphp
                                <td
                                    class="{{ $cell ? 'cell-filled' : '' }}"
                                    @if($cell)
                                        style="background-color: {{ $cell['background'] }};"
                                    @endif
                                >{{ $cell['text'] ?? '' }}</td>
                            @endforeach
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
