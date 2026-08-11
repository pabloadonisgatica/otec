<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Validación de documento</title>

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

            <div class="max-w-md mx-auto bg-white shadow-sm rounded-xl border border-gray-200 p-8 text-center">

                @if($diploma)

                    <div class="text-4xl mb-4">✅</div>

                    <h1 class="text-lg font-semibold text-gray-900">
                        Documento válido
                    </h1>

                    <div class="mt-6 text-left space-y-3 text-sm">

                        <div>
                            <p class="text-gray-500">Participante</p>
                            <p class="font-medium text-gray-900">
                                {{ $diploma->snapshot['participant']['full_name'] ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-500">Curso</p>
                            <p class="font-medium text-gray-900">
                                {{ $diploma->execution->course_name ?? ($diploma->snapshot['course']['name'] ?? '-') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-500">Fecha de ejecución</p>
                            <p class="font-medium text-gray-900">
                                {{ $diploma->snapshot['execution']['start_date'] ?? '' }}
                                @if(!empty($diploma->snapshot['execution']['end_date']))
                                    — {{ $diploma->snapshot['execution']['end_date'] }}
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-500">Emitido</p>
                            <p class="font-medium text-gray-900">
                                {{ optional($diploma->issued_at)->format('d-m-Y') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-500">Código</p>
                            <p class="font-mono text-xs text-gray-700">{{ $diploma->code }}</p>
                        </div>

                    </div>

                @else

                    <div class="text-4xl mb-4">❌</div>

                    <h1 class="text-lg font-semibold text-gray-900">
                        No pudimos verificar este documento
                    </h1>

                    <p class="text-sm text-gray-500 mt-2">
                        El código <span class="font-mono">{{ $code }}</span> no corresponde a
                        ningún documento emitido por esta plataforma.
                    </p>

                @endif

            </div>

        </div>

    </body>
</html>
