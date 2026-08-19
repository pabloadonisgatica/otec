<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Registro de Capacitación Interna</title>
    <style>
        @page { margin: 40px 50px; }

        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 11px;
            color: #1f2937;
        }

        .title-box {
            border: 1.5px solid #1f2937;
            padding: 14px;
            text-align: center;
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 12px;
            text-transform: uppercase;
            margin-top: 18px;
            margin-bottom: 6px;
        }

        table.meta td {
            padding: 3px 0;
        }

        table.meta td.label {
            font-weight: bold;
            width: 220px;
        }

        .box {
            border: 1px solid #1f2937;
            padding: 8px;
        }

        table.participants {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        table.participants th, table.participants td {
            border: 1px solid #1f2937;
            padding: 6px 8px;
            text-align: left;
            font-size: 10px;
        }

        table.participants th {
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    <div class="title-box">
        SISTEMA DE GESTIÓN DE CALIDAD - NCH 2728:2015 - REGISTRO DE CAPACITACIÓN INTERNA
    </div>

    <h2>Registro de Capacitación Interna</h2>
    <table class="meta">
        <tr><td class="label">FECHA</td><td>: {{ $training->activity_date->format('d/m/Y') }}</td></tr>
        <tr><td class="label">NOMBRE DE LA ACTIVIDAD</td><td>: {{ $training->activity_name }}</td></tr>
        <tr><td class="label">NOMBRE DEL RELATOR</td><td>: {{ $training->instructor_name ?? '—' }}</td></tr>
        <tr><td class="label">N° DE HORAS</td><td>: {{ $training->hours ?? '—' }}</td></tr>
    </table>

    <h2>Objetivo de la Capacitación</h2>
    <div class="box">{!! nl2br(e($training->objective)) ?: '—' !!}</div>

    <h2>Registro de Participantes</h2>
    <table class="participants">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Cargo</th>
                <th>Firma</th>
            </tr>
        </thead>
        <tbody>
            @forelse($training->participants as $participant)
                <tr>
                    <td>{{ $participant->name }}</td>
                    <td>{{ $participant->position ?? '—' }}</td>
                    <td>&nbsp;</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Sin participantes registrados</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Verificación de Eficacia de la Actividad</h2>
    <div class="box">
        CUMPLIMIENTO DE OBJETIVO:
        {{ is_null($training->objective_met) ? 'Sin evaluar' : ($training->objective_met ? 'SI' : 'NO') }}
    </div>

    <h2>Descripción de Cumplimiento del Objetivo</h2>
    <div class="box">{!! nl2br(e($training->compliance_description)) ?: '—' !!}</div>

    <h2>Requiere Acciones Adicionales</h2>
    <div class="box">{!! nl2br(e($training->additional_actions)) ?: '—' !!}</div>

</body>
</html>
