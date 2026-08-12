<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Informe Proveedor - {{ $provider->name }}</title>
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

        td {
            padding: 5px 4px;
            vertical-align: top;
        }

        .label {
            color: #6b7280;
            width: 160px;
            font-weight: bold;
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
        <h1>Informe de Proveedor</h1>
        <p>{{ $otecName }} — Gestión de Calidad NCH 2728:2015</p>
    </div>

    <h2>Datos Generales</h2>
    <table>
        <tr>
            <td class="label">Nombre / Razón social</td>
            <td>{{ $provider->name }}</td>
        </tr>
        @if($provider->business_name)
        <tr>
            <td class="label">Giro</td>
            <td>{{ $provider->business_name }}</td>
        </tr>
        @endif
        <tr>
            <td class="label">RUT</td>
            <td>{{ $provider->rut ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Categoría / Qué provee</td>
            <td>{{ $provider->category ?? '—' }}</td>
        </tr>
    </table>

    <h2>Contacto</h2>
    <table>
        <tr>
            <td class="label">Email</td>
            <td>{{ $provider->email ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Teléfono</td>
            <td>{{ $provider->phone ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Dirección</td>
            <td>
                {{ $provider->address ?? '—' }}
                @if($provider->commune) — {{ $provider->commune }} @endif
                @if($provider->region) , {{ $provider->region }} @endif
            </td>
        </tr>
    </table>

    @if($provider->contact_name || $provider->contact_email || $provider->contact_phone)
        <h2>Contacto Principal</h2>
        <table>
            <tr>
                <td class="label">Nombre</td>
                <td>{{ $provider->contact_name ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Email</td>
                <td>{{ $provider->contact_email ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Teléfono</td>
                <td>{{ $provider->contact_phone ?? '—' }}</td>
            </tr>
        </table>
    @endif

    @if($provider->notes)
        <h2>Notas</h2>
        <p>{{ $provider->notes }}</p>
    @endif

    <div class="footer">
        Documento generado el {{ now()->format('d-m-Y H:i') }} — {{ $otecName }}
    </div>

</body>
</html>
