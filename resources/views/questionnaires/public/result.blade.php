@extends('questionnaires.public.layout')

{{-- Resultado de un instrumento cerrado, para quien acaba de responder. --}}
@section('content')
    <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6 sm:p-8">
        <p class="text-xs font-semibold uppercase tracking-wider text-indigo-700">Resultados</p>
        <h1 class="mt-1 text-xl font-semibold text-gray-900">{{ $instrument->title() }}</h1>
        @include('questionnaires.public._context')
        <p class="mt-3 text-sm text-gray-700">
            Gracias por completar el cuestionario, <strong>{{ $response->respondentName() }}</strong>.
        </p>

        <div class="mt-6 border-t border-gray-200 pt-6">
            @include('questionnaires.instruments.' . $instrument->key() . '._scores', ['result' => $response->result])
        </div>

        <p class="mt-6 text-sm font-semibold text-gray-800">
            Para una óptima interpretación de estos resultados, le sugerimos revisarlos con el facilitador.
        </p>

        @if ($emailed)
            <div class="mt-4 rounded-lg bg-indigo-50 p-4 text-sm text-indigo-800">
                Enviamos una copia de estos resultados a <strong>{{ $response->email }}</strong>. Si no la ves, revisa tu carpeta de spam.
            </div>
        @else
            <div class="mt-4 rounded-lg bg-amber-50 p-4 text-sm text-amber-800">
                No pudimos enviar el correo automáticamente. Guarda esta pantalla (captura o imprimir) y compártela con tu facilitador.
            </div>
        @endif

        <div class="mt-6 text-center print:hidden">
            <button type="button" onclick="window.print()"
                    class="px-5 py-2 rounded-lg border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">Imprimir</button>
        </div>
    </div>
@endsection
