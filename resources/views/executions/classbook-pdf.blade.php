<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Libro de Control de Clases - {{ $execution->internal_code }}</title>
    <style>
        @page {
            margin: 25px 30px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #111;
        }

        h1 {
            font-size: 15px;
            text-align: center;
            text-transform: uppercase;
            margin: 0 0 12px 0;
        }

        h2 {
            font-size: 11px;
            text-transform: uppercase;
            background: #eee;
            padding: 4px 6px;
            margin: 18px 0 6px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            padding: 2px 4px;
            font-size: 9.5px;
            vertical-align: top;
        }

        .header-table .label {
            font-weight: bold;
            width: 230px;
        }

        .data-table {
            margin-top: 4px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #999;
            padding: 4px;
            font-size: 8.5px;
            text-align: left;
            vertical-align: middle;
        }

        .data-table th {
            background: #f0f0f0;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .signature-cell {
            height: 26px;
        }

        .page-break {
            page-break-before: always;
        }

        .small-note {
            font-size: 8px;
            color: #555;
            margin-top: 4px;
        }
    </style>
</head>
<body>

    <h1>Libro de Control de Clases</h1>

    <table class="header-table">
        <tr>
            <td class="label">CÓDIGO INTERNO CURSO:</td>
            <td>{{ $execution->course->folio ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">NOMBRE DE OTEC:</td>
            <td>{{ $otecName }}</td>
        </tr>
        <tr>
            <td class="label">NOMBRE ACTIVIDAD DE CAPACITACIÓN:</td>
            <td>{{ $execution->course_name }}</td>
        </tr>
        <tr>
            <td class="label">CÓDIGO AUTORIZADO POR SENCE:</td>
            <td>{{ $execution->course->sence_code ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">FECHA DE EJECUCIÓN:</td>
            <td>
                FECHA INICIO: {{ \Carbon\Carbon::parse($execution->start_date)->format('d/m/Y') }}
                &nbsp;&nbsp;&nbsp;
                FECHA TÉRMINO: {{ $execution->end_date ? \Carbon\Carbon::parse($execution->end_date)->format('d/m/Y') : '—' }}
            </td>
        </tr>
        <tr>
            <td class="label">LUGAR DE EJECUCIÓN:</td>
            <td>{{ $execution->place ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">HORARIO:</td>
            <td>{{ $execution->scheduleSummary() }}</td>
        </tr>
        <tr>
            <td class="label">DURACIÓN:</td>
            <td>{{ $execution->course_hours }} horas</td>
        </tr>
        <tr>
            <td class="label">NOMBRE(S) DE RELATOR(ES):</td>
            <td>{{ $execution->instructors->pluck('name')->implode(' / ') ?: '—' }}</td>
        </tr>
        <tr>
            <td class="label">OBSERVACIONES:</td>
            <td>{{ $execution->observations ?? '' }}</td>
        </tr>
    </table>

    {{-- ANTECEDENTES PARTICIPANTES --}}
    <h2>Antecedentes Participantes</h2>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20px;">N°</th>
                <th>Apellidos, Nombre</th>
                <th>RUT</th>
                <th>Email Participante</th>
                <th>Empresa</th>
                <th>Cargo Desempeñado</th>
                <th style="width: 90px;">Firma</th>
            </tr>
        </thead>
        <tbody>
            @foreach($execution->participants as $i => $participant)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $participant->last_name }} {{ $participant->first_name }}</td>
                    <td>{{ $participant->rut }}</td>
                    <td>{{ $participant->email }}</td>
                    <td>{{ $participant->company->name ?? '—' }}</td>
                    <td></td>
                    <td class="signature-cell"></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- CONTROL DE ASISTENCIA --}}
    <div class="page-break"></div>

    <h2>
        Control de Asistencia de Participantes
        &nbsp;&nbsp;
        Relator(es): {{ $execution->instructors->pluck('name')->implode(' / ') ?: '—' }}
    </h2>

    @php
        $orderedSessions = $execution->sessions->sortBy('session_date')->values();
    @endphp

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20px;">N°</th>
                <th>Apellidos, Nombre</th>
                @foreach($orderedSessions as $session)
                    <th>
                        {{ \Carbon\Carbon::parse($session->session_date)->format('d-m-Y') }}
                        @if($session->start_time)
                            <br>{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}
                            a {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                        @endif
                        <br>Firma
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($execution->participants as $i => $participant)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $participant->last_name }} {{ $participant->first_name }}</td>
                    @foreach($orderedSessions as $session)
                        <td class="signature-cell"></td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- TEMAS TRATADOS --}}
    <div class="page-break"></div>

    <h2>Contenidos Tratados por Sesión</h2>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 70px;">Fecha</th>
                <th>Temas / Actividades</th>
                <th style="width: 55px;">Hora Inicio</th>
                <th style="width: 55px;">Hora Término</th>
                <th style="width: 100px;">Firma Instructor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orderedSessions as $session)
                <tr>
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($session->session_date)->format('d-m-Y') }}
                    </td>
                    <td class="signature-cell"></td>
                    <td class="text-center">
                        {{ $session->start_time ? \Carbon\Carbon::parse($session->start_time)->format('H:i') : '' }}
                    </td>
                    <td class="text-center">
                        {{ $session->end_time ? \Carbon\Carbon::parse($session->end_time)->format('H:i') : '' }}
                    </td>
                    <td class="signature-cell"></td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Sin sesiones generadas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- CONTROL DE EVALUACIONES --}}
    <div class="page-break"></div>

    <h2>Control de Evaluaciones</h2>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20px;">N°</th>
                <th>Apellidos, Nombre</th>
                <th style="width: 80px;">Nota Final</th>
                <th style="width: 160px;">Firma Conformidad Participantes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($execution->participants as $i => $participant)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $participant->last_name }} {{ $participant->first_name }}</td>
                    <td class="signature-cell"></td>
                    <td class="signature-cell"></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="small-note">
        Documento generado para impresión y registro manual durante la ejecución presencial.
        Generado el {{ now()->format('d-m-Y H:i') }}.
    </p>

</body>
</html>
