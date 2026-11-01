@extends('questionnaires.public.layout')

{{-- Escala de Evaluación de Grupo: formulario público (10 ítems, escala 1 a 8). --}}
@php
    $items = \App\Services\Questionnaires\Instruments\GroupEvaluationInstrument::ITEMS;
    $respondentLabels = \App\Models\Questionnaire::RESPONDENT_FIELDS;
@endphp

@section('content')
    <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6 sm:p-8">
        <h1 class="text-xl font-semibold text-gray-900">{{ $questionnaire->name }}</h1>

        @if ($questionnaire->description)
            <p class="mt-3 text-sm text-gray-700 whitespace-pre-line">{{ $questionnaire->description }}</p>
        @endif

        <div class="mt-5 rounded-lg bg-gray-50 p-4 text-sm text-gray-700">
            <p class="font-semibold text-gray-900">Instrucciones</p>
            <ol class="mt-2 list-decimal list-inside space-y-1">
                <li>El propósito de este cuestionario es efectuar un diagnóstico de la forma en que sus subordinados enfrentan la situación de grupo.</li>
                <li>Los ítems describen situaciones características del trabajo.</li>
                <li>Por favor, califique a sus subordinados directos, seleccionando un número de acuerdo con la siguiente escala.</li>
                <li>Generalice sus respuestas para que reflejen al grupo completo.</li>
            </ol>
            <p class="mt-3"><strong>Presencia del atributo:</strong> escala del 1 al 8 (bajo – moderado – alto).</p>
        </div>

        @if ($errors->any())
            <div class="mt-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700" data-error>
                <p class="font-semibold">Revisa {{ $errors->count() === 1 ? 'el dato marcado' : 'los ' . $errors->count() . ' datos marcados' }} en rojo.</p>
            </div>
        @endif
    </div>

    <form method="POST" action="{{ route('questionnaires.public.store', $questionnaire->token) }}"
          x-data="{ sending: false, answered: 0, count() { this.answered = this.$root.querySelectorAll('input[data-item]:checked').length } }"
          x-init="count()" @change="count()" @submit="sending = true"
          class="mt-4 space-y-4 pb-20">
        @csrf

        <div style="position:absolute; left:-10000px;" aria-hidden="true">
            <label>No completar <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
        </div>

        {{-- Datos de quien responde --}}
        <section class="bg-white shadow-sm rounded-xl border border-gray-200 p-6 sm:p-8">
            <h2 class="text-base font-semibold text-gray-900">Tus datos</h2>
            <p class="mt-1 text-sm text-gray-500">Te enviaremos el resultado a este correo.</p>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach ($respondentFields as $field => $mode)
                    <div>
                        <label for="respondent_{{ $field }}" class="block text-sm font-medium text-gray-700">
                            {{ $respondentLabels[$field] }}<span class="text-red-600"> *</span>
                        </label>
                        <input id="respondent_{{ $field }}" name="respondent[{{ $field }}]"
                               type="{{ $field === 'email' ? 'email' : 'text' }}" maxlength="150" required
                               value="{{ old("respondent.$field") }}"
                               class="mt-1 w-full rounded-md border-gray-300 @error("respondent.$field") border-red-400 @enderror">
                        @error("respondent.$field")
                            <p class="mt-1 text-sm text-red-600" data-error>{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Ítems --}}
        @foreach ($items as $number => $item)
            @php $key = "answers.$number"; @endphp

            <fieldset class="bg-white shadow-sm rounded-xl border p-6 sm:p-8 {{ $errors->has($key) ? 'border-red-300' : 'border-gray-200' }}"
                      @if ($errors->has($key)) data-error @endif>
                <legend class="sr-only">{{ $number }}. {{ $item['title'] }}</legend>
                <p class="text-sm font-semibold text-gray-900">{{ $number }}. {{ $item['title'] }}</p>

                <div class="mt-4 grid grid-cols-8 gap-1 sm:gap-2" role="radiogroup">
                    @foreach (range(1, 8) as $value)
                        <label class="cursor-pointer">
                            <input type="radio" name="answers[{{ $number }}]" value="{{ $value }}" data-item
                                   @checked((string) old($key) === (string) $value) required class="peer sr-only">
                            <span class="flex h-11 items-center justify-center rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 peer-checked:border-indigo-600 peer-checked:bg-indigo-600 peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-500">
                                {{ $value }}
                            </span>
                        </label>
                    @endforeach
                </div>

                <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-gray-500">
                    <p><strong class="text-gray-700">1</strong> · {{ $item['low'] }}</p>
                    <p class="sm:text-right"><strong class="text-gray-700">8</strong> · {{ $item['high'] }}</p>
                </div>

                @error($key)
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </fieldset>
        @endforeach

        <div class="flex justify-end">
            <button type="submit" :disabled="sending"
                    class="w-full sm:w-auto px-6 py-3 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-500 disabled:opacity-50"
                    x-text="sending ? 'Calculando resultado…' : 'Enviar respuestas'">
                Enviar respuestas
            </button>
        </div>

        {{-- Avance --}}
        <div class="fixed inset-x-0 bottom-0 z-10 border-t border-gray-200 bg-white/95 backdrop-blur">
            <div class="max-w-2xl mx-auto px-4 py-2">
                <p class="text-xs text-gray-600"><span x-text="answered"></span> de {{ count($items) }} ítems respondidos</p>
                <div class="mt-1 h-1.5 rounded-full bg-gray-100 overflow-hidden">
                    <div class="h-full bg-indigo-600 transition-all" :style="'width:' + (answered * 100 / {{ count($items) }}) + '%'"></div>
                </div>
            </div>
        </div>
    </form>

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var first = document.querySelector('fieldset[data-error], p[data-error]');
                if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        </script>
    @endif
@endsection
