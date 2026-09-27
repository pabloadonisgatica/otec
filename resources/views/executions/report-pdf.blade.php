<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Informe — {{ $course_name }}</title>
    <style>
        @page { margin: 48px 52px 60px 52px; }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #1f2937;
            line-height: 1.4;
        }

        /* ---------- Portada ---------- */
        .cover { page-break-after: always; }
        .cover-logo { height: 70px; margin-top: 10px; }
        .cover-body { margin-top: 230px; }
        .cover-rule { width: 60px; height: 4px; background: #1f2937; margin-bottom: 18px; }
        .cover-kicker { font-size: 12px; letter-spacing: 3px; text-transform: uppercase; color: #6b7280; }
        .cover-title { font-size: 28px; font-weight: bold; color: #111827; margin: 8px 0 10px; line-height: 1.15; }
        .cover-client { font-size: 15px; color: #374151; }
        .cover-dates { font-size: 11px; color: #6b7280; margin-top: 6px; }
        .cover-footer { margin-top: 300px; font-size: 10px; color: #6b7280; }

        /* ---------- Contenido ---------- */
        h1 { font-size: 16px; margin: 0 0 14px; letter-spacing: 1px; text-transform: uppercase; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th, td { border: 1px solid #d1d5db; padding: 5px 7px; vertical-align: top; text-align: left; }
        .head td, .head th { background: #f3f4f6; font-weight: bold; text-transform: uppercase; font-size: 9.5px; letter-spacing: .3px; }
        .sub td { background: #fafafa; font-weight: bold; }
        .label { width: 22%; color: #4b5563; }
        .num { width: 13%; text-align: right; }
        .num-label { width: 25%; color: #4b5563; }
        .scale td { border: none; padding: 8px 0 4px; font-style: italic; color: #4b5563; }
        .avg { width: 70px; text-align: center; font-weight: bold; }
        .text { white-space: pre-line; }
        .empty { color: #9ca3af; font-style: italic; }
        .keep { page-break-inside: avoid; }

        .footer { position: fixed; bottom: -40px; left: 0; right: 0; font-size: 8.5px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 6px; }
    </style>
</head>
<body>

@php $dash = '—'; @endphp

{{-- ================= PORTADA ================= --}}
<div class="cover">
    @if ($logo_path)
        <img src="{{ $logo_path }}" class="cover-logo">
    @endif

    <div class="cover-body">
        <div class="cover-rule"></div>
        <div class="cover-kicker">Informe</div>
        <div class="cover-title">{{ $course_name }}</div>
        @if ($company_name)
            <div class="cover-client">{{ $company_name }}</div>
        @endif
        @if ($dates)
            <div class="cover-dates">{{ ucfirst($dates) }}</div>
        @endif
    </div>

    @if ($otec_name)
        <div class="cover-footer">{{ $otec_name }}</div>
    @endif
</div>

<div class="footer">
    {{ $otec_name }}{{ $otec_name ? ' · ' : '' }}Informe {{ $course_name }} · {{ $execution->internal_code }}
</div>

{{-- ================= DATOS GENERALES ================= --}}
<h1>Informe</h1>

<table class="keep">
    <tr class="head"><td colspan="4">Datos generales</td></tr>
    <tr class="sub"><td colspan="4">Curso: {{ $course_name }}</td></tr>
    <tr>
        <td class="label">Cliente:</td>
        <td>{{ $company_name ?: $dash }}</td>
        <td class="num-label">Inscritos:</td>
        <td class="num">{{ $counts['enrolled'] }}</td>
    </tr>
    <tr>
        <td class="label">Relatores:</td>
        <td>{{ $instructors ?: $dash }}</td>
        <td class="num-label">Asistentes:</td>
        <td class="num">{{ $counts['attended'] ?? $dash }}</td>
    </tr>
    <tr>
        <td class="label">Fechas:</td>
        <td>{{ $dates ? ucfirst($dates) : $dash }}</td>
        <td class="num-label">Aprobados:</td>
        <td class="num">{{ $counts['approved'] ?? $dash }}</td>
    </tr>
    <tr>
        <td class="label">Lugar:</td>
        <td>{{ $place ?: $dash }}</td>
        <td class="num-label">Invitados:</td>
        <td class="num">{{ $manual->guests ?: 'NA' }}</td>
    </tr>
    <tr>
        <td class="label">Modalidad:</td>
        <td>{{ $modality ?: $dash }}</td>
        <td class="num-label">Encuestas respondidas:</td>
        <td class="num">{{ $survey ? $survey['responses'] : $dash }}</td>
    </tr>
    <tr>
        <td class="label">Orden de compra:</td>
        <td>{{ $manual->purchase_order ?: $dash }}</td>
        <td class="num-label">Factura:</td>
        <td class="num">{{ $manual->invoice_number ?: $dash }}</td>
    </tr>
</table>

{{-- ================= CARACTERÍSTICAS ================= --}}
<table class="keep">
    <tr class="head"><td colspan="2">Características de la capacitación</td></tr>
    <tr>
        <td class="label">Objetivo general:</td>
        <td class="text">{{ $objective ?: $dash }}</td>
    </tr>
</table>

{{-- ================= EVALUACIONES ================= --}}
@if ($survey && count($survey['sections']))
    <table>
        <tr class="head"><td colspan="2">Evaluaciones de los participantes</td></tr>
    </table>

    @foreach ($survey['sections'] as $index => $section)
        <table class="keep">
            @if ($section['scale'])
                <tr class="scale"><td colspan="2">{{ $section['scale'] }}</td></tr>
            @endif
            <tr class="head">
                <td>{{ chr(65 + $index) }}.- {{ $section['title'] }}</td>
                <td class="avg">Promedio</td>
            </tr>
            @foreach ($section['questions'] as $question)
                <tr>
                    <td>{{ $question['number'] }}. {{ $question['label'] }}</td>
                    <td class="avg">{{ $question['result'] }}</td>
                </tr>
            @endforeach
        </table>
    @endforeach
@else
    <table class="keep">
        <tr class="head"><td>Evaluaciones de los participantes</td></tr>
        <tr><td class="empty">Esta ejecución no tiene encuesta de satisfacción con preguntas de puntuación.</td></tr>
    </table>
@endif

{{-- ================= COMENTARIOS ================= --}}
@foreach ($survey['comments'] ?? [] as $comment)
    <table>
        <tr class="head"><td>{{ $comment['label'] }}</td></tr>
        @foreach ($comment['texts'] as $text)
            <tr><td class="text">{{ $text }}</td></tr>
        @endforeach
    </table>
@endforeach

{{-- ================= OBSERVACIONES ================= --}}
<table class="keep">
    <tr class="head"><td colspan="2">Observaciones y sugerencias</td></tr>
    <tr>
        <td class="label">Observaciones:</td>
        <td class="text">{{ $manual->observations ?: $dash }}</td>
    </tr>
    <tr>
        <td class="label">Sugerencias:</td>
        <td class="text">{{ $manual->suggestions ?: $dash }}</td>
    </tr>
</table>

</body>
</html>
