@extends('questionnaires.public.layout')

{{-- Habilidades Gerenciales y Liderazgo: formulario público (12 situaciones, alternativas A-D). --}}
@php
    $questions = \App\Services\Questionnaires\Instruments\LeadershipInstrument::QUESTIONS;
    $respondentLabels = \App\Models\Questionnaire::RESPONDENT_FIELDS;
    $placeholders = ['email' => 'Ejemplo: tuemail@mail.cl', 'organization' => 'Ejemplo: nombre de la empresa'];
@endphp

@section('content')
    <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6 sm:p-8">
        <h1 class="text-xl font-semibold text-gray-900">{{ $questionnaire->name }}</h1>
        @include('questionnaires.public._context')

        @if ($questionnaire->description)
            <p class="mt-3 text-sm text-gray-700 whitespace-pre-line">{{ $questionnaire->description }}</p>
        @endif

        <div class="mt-5 rounded-lg bg-gray-50 p-4 text-sm text-gray-700 space-y-3">
            <p class="font-semibold text-gray-900">Instrucciones</p>
            <p>
                Para responder la siguiente encuesta, es necesario que usted imagine que está dirigiendo un grupo de trabajo
                y se encuentra involucrado en cada una de las siguientes situaciones. Cada una de éstas tiene cuatro
                alternativas de posibles acciones que puede emprender.
            </p>
            <p>
                Lea cuidadosamente cada punto. Seleccione la alternativa que mejor describe su comportamiento habitual ante
                situaciones similares. No responda de acuerdo con lo que se debería hacer, sino <strong>“LO QUE USTED SUELE HACER”</strong>.
                Se trata de obtener una "fotografía" de su propio estilo.
            </p>
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
                               value="{{ old("respondent.$field") }}" placeholder="{{ $placeholders[$field] ?? '' }}"
                               class="mt-1 w-full rounded-md border-gray-300 @error("respondent.$field") border-red-400 @enderror">
                        @error("respondent.$field")
                            <p class="mt-1 text-sm text-red-600" data-error>{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Situaciones --}}
        @foreach ($questions as $number => $question)
            @php $key = "answers.$number"; @endphp

            <fieldset class="bg-white shadow-sm rounded-xl border p-6 sm:p-8 {{ $errors->has($key) ? 'border-red-300' : 'border-gray-200' }}"
                      @if ($errors->has($key)) data-error @endif>
                <legend class="sr-only">Pregunta {{ $number }}</legend>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Pregunta {{ $number }} de {{ count($questions) }}</p>

                <p class="mt-3 text-sm font-semibold text-gray-900">Situación</p>
                <p class="mt-1 text-sm text-gray-700">{{ $question['situation'] }}</p>

                <p class="mt-4 text-sm font-semibold text-gray-900">Acciones alternativas</p>
                <div class="mt-2 space-y-2">
                    @foreach ($question['options'] as $letter => $option)
                        <label class="flex items-start gap-3 rounded-lg border border-gray-200 px-4 py-3 cursor-pointer hover:bg-gray-50 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                            <input type="radio" name="answers[{{ $number }}]" value="{{ $letter }}" data-item required
                                   @checked(old($key) === $letter) class="mt-0.5 border-gray-300 text-indigo-600">
                            <span class="text-sm text-gray-800"><strong>{{ $letter }}</strong> {{ $option['text'] }}</span>
                        </label>
                    @endforeach
                </div>

                @error($key)
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </fieldset>
        @endforeach

        <div class="flex justify-end">
            <button type="submit" :disabled="sending"
                    class="w-full sm:w-auto px-6 py-3 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-500 disabled:opacity-50"
                    x-text="sending ? 'Calculando resultado…' : 'Ver resultado'">
                Ver resultado
            </button>
        </div>

        {{-- Avance --}}
        <div class="fixed inset-x-0 bottom-0 z-10 border-t border-gray-200 bg-white/95 backdrop-blur">
            <div class="max-w-2xl mx-auto px-4 py-2">
                <p class="text-xs text-gray-600"><span x-text="answered"></span> de {{ count($questions) }} preguntas respondidas</p>
                <div class="mt-1 h-1.5 rounded-full bg-gray-100 overflow-hidden">
                    <div class="h-full bg-indigo-600 transition-all" :style="'width:' + (answered * 100 / {{ count($questions) }}) + '%'"></div>
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
