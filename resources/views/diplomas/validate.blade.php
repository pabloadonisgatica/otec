<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex">

        <title>Verificación de diplomas{{ $otecName ? ' — ' . $otecName : '' }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 min-h-screen">

        <div class="py-10 px-4">

            @if($appLogo ?? null)
                <div class="flex justify-center mb-6">
                    <img src="{{ Storage::url($appLogo) }}" alt="{{ $otecName ?? 'Logo' }}" class="h-14">
                </div>
            @endif

            <div class="max-w-md mx-auto space-y-4">

                {{-- Resultado --}}
                @if($code)
                    <div class="bg-white shadow-sm rounded-xl border p-8 {{ $diploma ? 'border-green-200' : 'border-red-200' }}">

                        @if($diploma)

                            <div class="flex items-center gap-3">
                                <div class="shrink-0 w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div>
                                    <h1 class="text-lg font-semibold text-gray-900">Diploma válido</h1>
                                    <p class="text-sm text-gray-500">
                                        Emitido por {{ $otecName ?: 'esta OTEC' }}
                                    </p>
                                </div>
                            </div>

                            <dl class="mt-6 divide-y divide-gray-100 text-sm">
                                @foreach ([
                                    'Participante' => $diploma->snapshot['participant']['full_name'] ?? null,
                                    'RUT' => $diploma->maskedRut(),
                                    'Curso' => $diploma->snapshot['course']['name'] ?? $diploma->execution?->course_name,
                                    'Horas' => ! empty($diploma->snapshot['course']['hours']) ? $diploma->snapshot['course']['hours'] . ' horas' : null,
                                    'Empresa' => $diploma->snapshot['execution']['company'] ?? null,
                                    'Fecha de ejecución' => trim(($diploma->snapshot['execution']['start_date'] ?? '') . (! empty($diploma->snapshot['execution']['end_date']) ? ' al ' . $diploma->snapshot['execution']['end_date'] : '')),
                                    'Fecha de emisión' => optional($diploma->issued_at)->format('d-m-Y'),
                                ] as $label => $value)
                                    @if(filled($value))
                                        <div class="flex justify-between gap-4 py-2.5">
                                            <dt class="text-gray-500">{{ $label }}</dt>
                                            <dd class="font-medium text-gray-900 text-right">{{ $value }}</dd>
                                        </div>
                                    @endif
                                @endforeach
                                <div class="flex justify-between gap-4 py-2.5">
                                    <dt class="text-gray-500">Código</dt>
                                    <dd class="font-mono text-gray-700">{{ $diploma->code }}</dd>
                                </div>
                            </dl>

                        @else

                            <div class="flex items-center gap-3">
                                <div class="shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </div>
                                <div>
                                    <h1 class="text-lg font-semibold text-gray-900">No encontramos este diploma</h1>
                                    <p class="text-sm text-gray-500">
                                        El código <span class="font-mono">{{ $code }}</span> no corresponde a ningún
                                        diploma vigente emitido por {{ $otecName ?: 'esta OTEC' }}.
                                    </p>
                                </div>
                            </div>

                        @endif

                    </div>
                @endif

                {{-- Verificar por código --}}
                <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6">
                    <h2 class="text-sm font-semibold text-gray-900">
                        {{ $code ? 'Verificar otro diploma' : 'Verificar un diploma' }}
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">
                        Ingresa el código que aparece bajo el QR del diploma.
                    </p>

                    <form method="GET" action="{{ route('diplomas.verify') }}" class="mt-4 flex gap-2">
                        <input type="text" name="code" required maxlength="20" autocomplete="off"
                               placeholder="Ej: A1B2C3D4E5"
                               class="flex-1 rounded-lg border-gray-300 text-sm font-mono uppercase">
                        <button type="submit"
                                class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-500">
                            Verificar
                        </button>
                    </form>
                </div>

            </div>

        </div>

    </body>
</html>
