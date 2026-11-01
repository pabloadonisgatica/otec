Gracias por completar el cuestionario

Nombre: {{ $response->respondentName() }}
Correo: {{ $response->email }}

{{ $instrument->title() }}
@include('emails.questionnaires.' . $instrument->key() . '-text', ['result' => $response->result])

Para una óptima interpretación de estos resultados, te sugerimos revisarlos con el facilitador.
{{ $otecName }}
