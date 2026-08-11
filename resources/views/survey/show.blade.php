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

            @if($appLogo ?? null)
                <div class="flex justify-center mb-6">
                    <img src="{{ Storage::url($appLogo) }}" alt="Logo" class="h-14">
                </div>
            @endif

            <div class="max-w-2xl mx-auto bg-white shadow-sm rounded-xl border border-gray-200 p-6 sm:p-8">

                <div class="mb-6">
                    <h1 class="text-lg font-semibold text-gray-900">
                        Encuesta de satisfacción
                    </h1>

                    <p class="text-sm text-gray-500 mt-1">
                        {{ $execution->course_name }}
                    </p>

                    <p class="text-xs text-gray-400 mt-1">
                        {{ \Carbon\Carbon::parse($execution->start_date)->format('d-m-Y') }}
                        @if($execution->end_date)
                            — {{ \Carbon\Carbon::parse($execution->end_date)->format('d-m-Y') }}
                        @endif
                    </p>
                </div>

                <p class="text-sm text-gray-700 mb-8">
                    Hola <strong>{{ $participant->first_name }}</strong>, tu opinión nos ayuda a mejorar. N/A = No aplica.
                </p>

                <form method="POST" action="{{ route('survey.store', $response->token) }}" class="space-y-10">

                    @csrf

                    @foreach($questions as $area => $areaQuestions)

                        <div>

                            <h2 class="text-sm font-semibold text-indigo-700 uppercase mb-4">
                                {{ $area }}
                            </h2>

                            <div class="space-y-7">

                                @foreach($areaQuestions as $question)

                                    <div x-data="{ value: '{{ old('scores.' . $question->id, '') }}' }">

                                        <p class="text-sm font-medium text-gray-900 mb-3">
                                            {{ $question->text }}
                                        </p>

                                        <div class="grid grid-cols-4 sm:grid-cols-8 gap-2">

                                            @foreach([1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 'N/A' => ''] as $label => $value)

                                                <label
                                                    class="flex flex-col items-center gap-1 border rounded-lg py-2 text-xs cursor-pointer transition"
                                                    :class="value === '{{ $value }}'
                                                        ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                                                        : 'border-gray-300 hover:bg-gray-50'">

                                                    <input
                                                        type="radio"
                                                        name="scores[{{ $question->id }}]"
                                                        value="{{ $value }}"
                                                        x-model="value"
                                                        class="h-4 w-4">

                                                    {{ $label }}

                                                </label>

                                            @endforeach

                                        </div>

                                    </div>

                                @endforeach

                            </div>

                        </div>

                    @endforeach

                    <div>

                        <label class="block text-sm font-medium text-gray-900 mb-2">
                            Sugerencias y reclamos
                        </label>

                        <textarea
                            name="suggestions"
                            rows="4"
                            class="w-full rounded-lg border-gray-300"
                            placeholder="Opcional">{{ old('suggestions') }}</textarea>

                    </div>

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
