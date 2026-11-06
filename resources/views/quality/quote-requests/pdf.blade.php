<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Solicitud de Cotización {{ $quoteRequest->code }}</title>
    <style>
        @page { margin: 40px 50px; }

        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 11px;
            color: #1f2937;
        }

        .header {
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .header table td { padding: 0; vertical-align: middle; }

        .header img { max-height: 48px; max-width: 160px; }

        .header h1 {
            font-size: 18px;
            margin: 0 0 4px 0;
            color: #1f2937;
        }

        .header p {
            font-size: 10px;
            color: #6b7280;
            margin: 0;
        }

        .code {
            font-size: 13px;
            font-weight: bold;
            color: #4f46e5;
            text-align: right;
        }

        h2 {
            font-size: 12px;
            text-transform: uppercase;
            color: #4f46e5;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 4px;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        td, th {
            padding: 5px 4px;
            vertical-align: top;
            text-align: left;
        }

        .label {
            color: #6b7280;
            width: 160px;
            font-weight: bold;
        }

        .items th {
            background: #f3f4f6;
            font-size: 10px;
            color: #4b5563;
            border-bottom: 1px solid #d1d5db;
        }

        .items td { border-bottom: 1px solid #e5e7eb; }

        .num { text-align: right; }

        .note {
            margin-top: 20px;
            padding: 10px 12px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            font-size: 10px;
        }

        .footer {
            margin-top: 40px;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    @if($logo)
                        <img src="{{ $logo }}" alt=""><br>
                    @endif
                    <h1>Solicitud de Cotización</h1>
                    <p>{{ $otecName }}</p>
                </td>
                <td class="code">{{ $quoteRequest->code }}</td>
            </tr>
        </table>
    </div>

    <h2>Proveedor</h2>
    <table>
        <tr>
            <td class="label">Nombre / Razón social</td>
            <td>{{ $quoteRequest->provider->name }}</td>
        </tr>
        @if($quoteRequest->provider->rut)
            <tr>
                <td class="label">RUT</td>
                <td>{{ $quoteRequest->provider->rut }}</td>
            </tr>
        @endif
        @if($quoteRequest->provider->contact_name)
            <tr>
                <td class="label">Atención</td>
                <td>
                    {{ $quoteRequest->provider->contact_name }}
                    @if($quoteRequest->provider->contact_email) — {{ $quoteRequest->provider->contact_email }} @endif
                </td>
            </tr>
        @endif
    </table>

    <h2>Datos de la solicitud</h2>
    <table>
        <tr>
            <td class="label">Fecha de solicitud</td>
            <td>{{ $quoteRequest->requested_at->format('d-m-Y') }}</td>
        </tr>
        @if($quoteRequest->needed_by)
            <tr>
                <td class="label">Responder antes del</td>
                <td>{{ $quoteRequest->needed_by->format('d-m-Y') }}</td>
            </tr>
        @endif
        @if($quoteRequest->execution)
            <tr>
                <td class="label">Curso</td>
                <td>{{ $quoteRequest->execution->course_name }} ({{ $quoteRequest->execution->internal_code }})</td>
            </tr>
        @endif
        @if($quoteRequest->requester)
            <tr>
                <td class="label">Solicitado por</td>
                <td>
                    {{ $quoteRequest->requester->name }}
                    @if($quoteRequest->requester->email) — {{ $quoteRequest->requester->email }} @endif
                </td>
            </tr>
        @endif
    </table>

    <h2>Ítems a cotizar</h2>
    <table class="items">
        <thead>
            <tr>
                <th style="width: 24px;">#</th>
                <th>Descripción</th>
                <th class="num" style="width: 70px;">Cantidad</th>
                <th style="width: 90px;">Unidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quoteRequest->items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item['description'] }}</td>
                    <td class="num">{{ \App\Models\ProviderQuoteRequest::formatQuantity($item['quantity']) }}</td>
                    <td>{{ $item['unit'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($quoteRequest->conditions)
        <h2>Condiciones</h2>
        <p style="white-space: pre-line;">{{ $quoteRequest->conditions }}</p>
    @endif

    <div class="note">
        Agradecemos enviar su cotización indicando precio unitario y total (neto e IVA cuando corresponda),
        plazo de entrega, forma de pago y vigencia de la oferta, haciendo referencia al código {{ $quoteRequest->code }}.
    </div>

    <div class="footer">
        Documento generado el {{ now()->format('d-m-Y H:i') }} — {{ $otecName }} — Gestión de Calidad NCH 2728:2015
    </div>

</body>
</html>
