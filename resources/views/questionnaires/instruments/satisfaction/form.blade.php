{{--
    Encuesta de satisfacción libre: misma página que responde el participante
    en la encuesta de la ejecución (execution_survey/show), con un solo relator.
--}}
@php
    $bannerUrl = $questionnaire->bannerUrl() ?? $instrument->templateBannerUrl();
    $description = $questionnaire->description ?: $instrument->templateDescription();
    $instructorName = $instrument->instructorName();
@endphp
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex, nofollow">
        <title>Encuesta de satisfacción</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 min-h-screen">

        @auth
            <div class="bg-gray-800 text-gray-100 text-sm">
                <div class="max-w-2xl mx-auto px-4 py-2 flex flex-wrap items-center justify-between gap-2">
                    <span>Vista de administrador · Estado: <strong>{{ $questionnaire->statusLabel() }}</strong></span>
                    <a href="{{ route('questionnaires.edit', $questionnaire) }}" class="underline">Volver a la encuesta</a>
                </div>
            </div>
        @endauth

        <div class="py-10 px-4">
            <div class="max-w-2xl mx-auto">

                {{-- Banner --}}
                @if ($bannerUrl)
                    <div class="mb-4 rounded-xl overflow-hidden">
                        <img src="{{ $bannerUrl }}" alt="Banner" class="w-full object-cover max-h-40">
                    </div>
                @endif

                <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6 sm:p-8">

                    <h1 class="text-lg font-semibold text-gray-900">Encuesta de satisfacción</h1>
                    <p class="text-sm text-gray-500 mt-1">{{ $questionnaire->name }}</p>
                    @include('questionnaires.public._context')

                    @if ($description)
                        <div class="mt-4 text-sm text-gray-700 whitespace-pre-line border-l-2 border-indigo-200 pl-4">
                            {{ $description }}
                        </div>
                    @endif

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
                          action="{{ route('questionnaires.public.store', $questionnaire->token) }}"
                          x-data="{ sending: false }" @submit="sending = true"
                          class="mt-8 space-y-8">

                        @csrf

                        {{-- Campo trampa anti-bots --}}
                        <div style="position:absolute; left:-10000px;" aria-hidden="true">
                            <label>No completar <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                        </div>

                        <div class="rounded-xl border-2 border-indigo-100 bg-indigo-50/40 p-5">

                            {{-- Identidad del relator --}}
                            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-indigo-100">
                                <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-base shrink-0">
                                    {{ mb_strtoupper(mb_substr($instructorName, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-xs text-indigo-600 font-semibold uppercase tracking-wide">Evaluando a</p>
                                    <p class="text-base font-bold text-gray-900">{{ $instructorName }}</p>
                                </div>
                            </div>

                            {{-- Todas las secciones de la plantilla --}}
                            @foreach ($instrument->sections() as $section)
                                <div class="{{ ! $loop->first ? 'mt-7 pt-6 border-t border-indigo-100' : '' }}">

                                    @if ($section['title'])
                                        <h2 class="text-sm font-semibold text-indigo-700 uppercase tracking-wide mb-1">
                                            {{ $section['title'] }}
                                        </h2>
                                    @endif

                                    @if ($section['description'])
                                        <p class="text-xs text-gray-500 mb-4 whitespace-pre-line">
                                            {{ $section['description'] }}
                                        </p>
                                    @else
                                        <div class="mb-4"></div>
                                    @endif

                                    <div class="space-y-7">
                                        @foreach ($section['fields'] as $field)
                                            @include('execution_survey.partials.field', [
                                                'field'   => (object) $field,
                                                'name'    => "answers[{$field['number']}]",
                                                'old_key' => "answers.{$field['number']}",
                                            ])
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach

                        </div>

                        <button type="submit" :disabled="sending"
                                class="w-full px-5 py-3 rounded-lg bg-indigo-600 text-white font-bold text-base hover:bg-indigo-700 transition disabled:opacity-50">
                            Enviar respuestas
                        </button>

                    </form>

                </div>
            </div>
        </div>

    </body>
</html>
