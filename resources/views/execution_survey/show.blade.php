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

        <div class="py-10 px-4">

            <div class="max-w-2xl mx-auto bg-white shadow-sm rounded-xl border border-gray-200 p-6 sm:p-8">

                <div class="mb-6">
                    <h1 class="text-lg font-semibold text-gray-900">
                        Encuesta de satisfacción
                    </h1>

                    <p class="text-sm text-gray-500 mt-1">
                        {{ $execution->course->name ?? $execution->course_name ?? '' }}
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 p-3 rounded bg-red-50 text-red-700 text-sm">
                        Por favor revisa las preguntas obligatorias antes de enviar.
                    </div>
                @endif

                <form method="POST" action="{{ route('execution-survey.public.store', $executionSurvey->token) }}" class="space-y-10">

                    @csrf

                    @foreach ($generalSections as $section)
                        <div>
                            <h2 class="text-sm font-semibold text-indigo-700 uppercase mb-1">
                                {{ $section->title }}
                            </h2>
                            @if ($section->description)
                                <p class="text-xs text-gray-500 mb-4">{{ $section->description }}</p>
                            @else
                                <div class="mb-4"></div>
                            @endif

                            <div class="space-y-7">
                                @foreach ($section->fields as $field)
                                    @include('execution_survey.partials.field', [
                                        'field' => $field,
                                        'name' => "general[{$field->id}]",
                                        'old_key' => "general.{$field->id}",
                                    ])
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    @foreach ($repeatingSections as $section)
                        @foreach ($instructors as $instructor)
                            <div>
                                <h2 class="text-sm font-semibold text-indigo-700 uppercase mb-1">
                                    {{ $section->title }}
                                </h2>
                                @if ($section->description)
                                    <p class="text-xs text-gray-500 mb-1">{{ $section->description }}</p>
                                @endif
                                <p class="text-xs text-gray-500 mb-4">
                                    Evaluación de: <strong>{{ $instructor->name }}</strong>
                                </p>

                                <div class="space-y-7">
                                    @foreach ($section->fields as $field)
                                        @include('execution_survey.partials.field', [
                                            'field' => $field,
                                            'name' => "instructors[{$instructor->id}][{$field->id}]",
                                            'old_key' => "instructors.{$instructor->id}.{$field->id}",
                                        ])
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @endforeach

                    <button
                        type="submit"
                        class="w-full px-5 py-3 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700">
                        Enviar respuestas
                    </button>

                </form>

            </div>

        </div>

    </body>
</html>
