{{-- Correo con el resultado de un instrumento cerrado (estilos en línea para clientes de correo). --}}
<div style="font-family: Arial, Helvetica, sans-serif; font-size: 15px; color: #333; max-width: 540px;">
    <h2 style="font-size: 20px; margin: 0 0 12px;">Gracias por completar el cuestionario</h2>

    <p style="margin: 0 0 16px;">
        <b>Nombre:</b> {{ $response->respondentName() }}<br>
        <b>Correo:</b> {{ $response->email }}
        @if ($response->organization)
            <br><b>Organización:</b> {{ $response->organization }}
        @endif
    </p>

    <p style="margin: 0 0 8px;"><b>{{ $instrument->title() }}</b></p>

    @include('emails.questionnaires.' . $instrument->key(), ['result' => $response->result])

    <p style="color: #666; font-size: 13px; margin: 16px 0 0;">
        Para una óptima interpretación de estos resultados, te sugerimos revisarlos con el facilitador.
        Este correo fue generado automáticamente{{ $otecName ? ' por ' . $otecName : '' }}.
    </p>
</div>
