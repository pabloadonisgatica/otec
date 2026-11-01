@extends('questionnaires.public.layout')

@section('content')
    @php
        $number = 0;
        $respondentLabels = \App\Models\Questionnaire::RESPONDENT_FIELDS;
        $respondentTypes = ['email' => 'email'];
        $scaleGrid = ['scale_5' => 'grid-cols-5', 'scale_10' => 'grid-cols-5 sm:grid-cols-10', 'nps' => 'grid-cols-6 sm:grid-cols-11'];
    @endphp

    <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6 sm:p-8">
        <h1 class="text-xl font-semibold text-gray-900">{{ $questionnaire->name }}</h1>
        @include('questionnaires.public._context')

        @if ($questionnaire->description)
            <p class="mt-3 text-sm text-gray-700 whitespace-pre-line">{{ $questionnaire->description }}</p>
        @endif

        <p class="mt-4 text-xs text-gray-500">Las preguntas marcadas con <span class="text-red-600">*</span> son obligatorias.</p>

        @if ($errors->any())
            <div class="mt-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700" data-error>
                <p class="font-semibold">Revisa {{ $errors->count() === 1 ? 'la respuesta marcada' : 'las ' . $errors->count() . ' respuestas marcadas' }} en rojo.</p>
            </div>
        @endif
    </div>

    <form method="POST" action="{{ route('questionnaires.public.store', $questionnaire->token) }}"
          x-data="{ sending: false }" @submit="sending = true"
          class="mt-4 space-y-4">
        @csrf

        {{-- Campo trampa: las personas no lo ven; si viene lleno, es un bot. --}}
        <div style="position:absolute; left:-10000px;" aria-hidden="true">
            <label>No completar <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
        </div>

        {{-- Datos de quien responde --}}
        @if ($respondentFields)
            <section class="bg-white shadow-sm rounded-xl border border-gray-200 p-6 sm:p-8">
                <h2 class="text-base font-semibold text-gray-900">Tus datos</h2>

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach ($respondentFields as $field => $mode)
                        <div>
                            <label for="respondent_{{ $field }}" class="block text-sm font-medium text-gray-700">
                                {{ $respondentLabels[$field] }}@if ($mode === 'required')<span class="text-red-600"> *</span>@endif
                            </label>
                            <input id="respondent_{{ $field }}" name="respondent[{{ $field }}]"
                                   type="{{ $respondentTypes[$field] ?? 'text' }}" maxlength="150"
                                   value="{{ old("respondent.$field") }}" @required($mode === 'required')
                                   class="mt-1 w-full rounded-md border-gray-300 @error("respondent.$field") border-red-400 @enderror">
                            @error("respondent.$field")
                                <p class="mt-1 text-sm text-red-600" data-error>{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Secciones y preguntas --}}
        @foreach ($sections as $section)
            <section class="bg-white shadow-sm rounded-xl border border-gray-200 p-6 sm:p-8 space-y-8">
                @if ($section->title || $section->description)
                    <div>
                        @if ($section->title)
                            <h2 class="text-base font-semibold text-gray-900">{{ $section->title }}</h2>
                        @endif
                        @if ($section->description)
                            <p class="mt-1 text-sm text-gray-600 whitespace-pre-line">{{ $section->description }}</p>
                        @endif
                    </div>
                @endif

                @foreach ($section->questions as $question)
                    @php
                        $number++;
                        $name = "answers[{$question->id}]";
                        $key = "answers.{$question->id}";
                        $old = old($key);
                        $hasError = $errors->has($key) || $errors->has($key . '.*');
                    @endphp

                    <fieldset @if ($hasError) data-error @endif>
                        <legend class="text-sm font-medium text-gray-900">
                            <span class="text-gray-400">{{ $number }}.</span>
                            {{ $question->label }}@if ($question->is_required)<span class="text-red-600"> *</span>@endif
                        </legend>

                        @if ($question->help)
                            <p class="mt-1 text-sm text-gray-500">{{ $question->help }}</p>
                        @endif

                        <div class="mt-3">
                            @switch($question->type)
                                @case('short_text')
                                    <input type="text" name="{{ $name }}" value="{{ $old }}" maxlength="500"
                                           class="w-full rounded-md border-gray-300 @if ($hasError) border-red-400 @endif">
                                    @break

                                @case('long_text')
                                    <textarea name="{{ $name }}" rows="4" maxlength="5000"
                                              class="w-full rounded-md border-gray-300 @if ($hasError) border-red-400 @endif">{{ $old }}</textarea>
                                    @break

                                @case('single_choice')
                                    <div class="space-y-2">
                                        @foreach ($question->options as $option)
                                            <label class="flex items-start gap-3 rounded-lg border border-gray-200 px-4 py-3 cursor-pointer hover:bg-gray-50 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                                                <input type="radio" name="{{ $name }}" value="{{ $option }}" @checked($old === $option)
                                                       class="mt-0.5 border-gray-300 text-indigo-600">
                                                <span class="text-sm text-gray-800">{{ $option }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @break

                                @case('multiple_choice')
                                    <p class="mb-2 text-xs text-gray-500">Puedes elegir más de una.</p>
                                    <div class="space-y-2">
                                        @foreach ($question->options as $option)
                                            <label class="flex items-start gap-3 rounded-lg border border-gray-200 px-4 py-3 cursor-pointer hover:bg-gray-50 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                                                <input type="checkbox" name="{{ $name }}[]" value="{{ $option }}" @checked(in_array($option, (array) $old, true))
                                                       class="mt-0.5 rounded border-gray-300 text-indigo-600">
                                                <span class="text-sm text-gray-800">{{ $option }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @break

                                @case('dropdown')
                                    <select name="{{ $name }}" class="w-full rounded-md border-gray-300 @if ($hasError) border-red-400 @endif">
                                        <option value="">Selecciona una opción…</option>
                                        @foreach ($question->options as $option)
                                            <option value="{{ $option }}" @selected($old === $option)>{{ $option }}</option>
                                        @endforeach
                                    </select>
                                    @break

                                @case('yes_no')
                                    <div class="grid grid-cols-2 gap-3 max-w-xs">
                                        @foreach (['Sí', 'No'] as $option)
                                            <label class="cursor-pointer">
                                                <input type="radio" name="{{ $name }}" value="{{ $option }}" @checked($old === $option) class="peer sr-only">
                                                <span class="block rounded-lg border border-gray-300 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-50 peer-checked:border-indigo-600 peer-checked:bg-indigo-600 peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-500">
                                                    {{ $option }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @break

                                @case('scale_5')
                                @case('scale_10')
                                @case('nps')
                                    @php [$min, $max] = \App\Models\QuestionnaireQuestion::SCALE_RANGES[$question->type]; @endphp
                                    <div class="grid {{ $scaleGrid[$question->type] }} gap-2">
                                        @foreach (range($min, $max) as $value)
                                            <label class="cursor-pointer">
                                                <input type="radio" name="{{ $name }}" value="{{ $value }}" @checked((string) $old === (string) $value) class="peer sr-only">
                                                <span class="flex h-11 items-center justify-center rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 peer-checked:border-indigo-600 peer-checked:bg-indigo-600 peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-500">
                                                    {{ $value }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @if (($question->settings['min_label'] ?? null) || ($question->settings['max_label'] ?? null))
                                        <div class="mt-2 flex justify-between gap-4 text-xs text-gray-500">
                                            <span>{{ $min }} = {{ $question->settings['min_label'] ?? '' }}</span>
                                            <span class="text-right">{{ $max }} = {{ $question->settings['max_label'] ?? '' }}</span>
                                        </div>
                                    @endif
                                    @break

                                @case('date')
                                    <input type="date" name="{{ $name }}" value="{{ $old }}"
                                           class="rounded-md border-gray-300 @if ($hasError) border-red-400 @endif">
                                    @break
                            @endswitch
                        </div>

                        @if ($hasError)
                            <p class="mt-2 text-sm text-red-600">{{ $errors->first($key) ?: $errors->first($key . '.*') }}</p>
                        @endif
                    </fieldset>
                @endforeach
            </section>
        @endforeach

        <div class="flex justify-end">
            <button type="submit" :disabled="sending"
                    class="w-full sm:w-auto px-6 py-3 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-500 disabled:opacity-50"
                    x-text="sending ? 'Enviando…' : 'Enviar respuestas'">
                Enviar respuestas
            </button>
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
