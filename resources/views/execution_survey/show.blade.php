<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Encuesta de satisfacción</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 min-h-screen">

        @php
            // Con un solo relator no hay nada que elegir: queda seleccionado.
            $singleInstructor = $instructors->count() === 1;
            $selectedInstructor = (string) old('instructor_id', $singleInstructor ? $instructors->first()->id : '');
        @endphp

        <div class="py-10 px-4">
            <div class="max-w-2xl mx-auto">

                {{-- Banner --}}
                @if ($executionSurvey->template->banner_path)
                    <div class="mb-4 rounded-xl overflow-hidden">
                        <img src="{{ $executionSurvey->template->bannerUrl() }}"
                             alt="Banner"
                             class="w-full object-cover max-h-40">
                    </div>
                @endif

                <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6 sm:p-8">

                    <h1 class="text-lg font-semibold text-gray-900">Encuesta de satisfacción</h1>
                    <p class="text-sm text-gray-500 mt-1">{{ $execution->course->name ?? '' }}</p>

                    {{-- Descripción global de la plantilla --}}
                    @if ($executionSurvey->template->description)
                        <div class="mt-4 text-sm text-gray-700 whitespace-pre-line border-l-2 border-indigo-200 pl-4">
                            {{ $executionSurvey->template->description }}
                        </div>
                    @endif

                    @if ($instructors->isEmpty())

                        <div class="mt-6 p-4 rounded-lg bg-amber-50 border border-amber-200 text-sm text-amber-800">
                            Esta encuesta aún no está disponible porque el curso no tiene relatores asignados.
                            Avísale a la OTEC.
                        </div>

                    @else

                        @if ($errors->any())
                            <div class="mt-6 p-4 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
                                <p class="font-semibold mb-2">Por favor completa todos los campos requeridos:</p>
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST"
                              action="{{ route('execution-survey.public.store', $executionSurvey->token) }}"
                              x-data="{
                                  instructor: @js($selectedInstructor),
                                  names: @js($instructors->pluck('name', 'id')),
                                  sending: false,
                              }"
                              @submit="sending = true"
                              class="mt-8 space-y-8">

                            @csrf

                            {{-- 1. Relator que le hizo clases --}}
                            @if ($singleInstructor)
                                <input type="hidden" name="instructor_id" value="{{ $instructors->first()->id }}">
                            @else
                                <fieldset>
                                    <legend class="text-sm font-semibold text-gray-900">
                                        ¿Qué relator te hizo clases? <span class="text-red-500">*</span>
                                    </legend>
                                    <p class="text-xs text-gray-500 mt-1">Elige a tu relator. Solo vas a evaluar a esa persona.</p>

                                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        @foreach ($instructors as $instructor)
                                            <label class="flex items-center gap-3 rounded-lg border p-3 cursor-pointer transition"
                                                   :class="instructor === '{{ $instructor->id }}'
                                                       ? 'border-indigo-500 bg-indigo-50'
                                                       : 'border-gray-300 hover:bg-gray-50'">
                                                <input type="radio" name="instructor_id" value="{{ $instructor->id }}"
                                                       x-model="instructor" class="h-4 w-4 text-indigo-600">
                                                <span class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-sm shrink-0">
                                                    {{ mb_strtoupper(mb_substr($instructor->name, 0, 1)) }}
                                                </span>
                                                <span class="text-sm font-medium text-gray-900">{{ $instructor->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>

                                    @error('instructor_id')
                                        <p class="text-red-600 text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </fieldset>

                                <p x-show="! instructor" class="text-sm text-gray-500">
                                    Cuando elijas a tu relator aparecerán las preguntas.
                                </p>
                            @endif

                            {{-- 2. Evaluación del relator elegido --}}
                            <div x-show="instructor" x-cloak class="rounded-xl border-2 border-indigo-100 bg-indigo-50/40 p-5">

                                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-indigo-100">
                                    <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-base shrink-0"
                                         x-text="(names[instructor] || '').charAt(0).toUpperCase()"></div>
                                    <div>
                                        <p class="text-xs text-indigo-600 font-semibold uppercase tracking-wide">Evaluando a</p>
                                        <p class="text-base font-bold text-gray-900" x-text="names[instructor]"></p>
                                    </div>
                                </div>

                                @foreach ($sections as $section)
                                    <div class="{{ ! $loop->first ? 'mt-7 pt-6 border-t border-indigo-100' : '' }}">

                                        @if ($section->title)
                                            <h2 class="text-sm font-semibold text-indigo-700 uppercase tracking-wide mb-1">
                                                {{ $section->title }}
                                            </h2>
                                        @endif

                                        @if ($section->description)
                                            <p class="text-xs text-gray-500 mb-4 whitespace-pre-line">
                                                {{ $section->description }}
                                            </p>
                                        @else
                                            <div class="mb-4"></div>
                                        @endif

                                        <div class="space-y-7">
                                            @foreach ($section->fields as $field)
                                                @include('execution_survey.partials.field', [
                                                    'field'   => $field,
                                                    'name'    => "answers[{$field->id}]",
                                                    'old_key' => "answers.{$field->id}",
                                                ])
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach

                            </div>

                            <button type="submit" :disabled="! instructor || sending"
                                    class="w-full px-5 py-3 rounded-lg bg-indigo-600 text-white font-bold text-base hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                Enviar respuestas
                            </button>

                        </form>

                    @endif

                </div>
            </div>
        </div>

    </body>
</html>
