{{-- Layout de las páginas públicas de Encuestas (sin login). --}}
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex, nofollow">
        <title>{{ $questionnaire->name }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 text-gray-900 min-h-screen">

        @auth
            <div class="bg-gray-800 text-gray-100 text-sm">
                <div class="max-w-2xl mx-auto px-4 py-2 flex flex-wrap items-center justify-between gap-2">
                    <span>Vista de administrador · Estado: <strong>{{ $questionnaire->statusLabel() }}</strong></span>
                    <a href="{{ route('questionnaires.edit', $questionnaire) }}" class="underline">Volver a la encuesta</a>
                </div>
            </div>
        @endauth

        <div class="py-8 px-4">
            <div class="max-w-2xl mx-auto">

                @if ($branding['logo'])
                    <div class="mb-6 flex justify-center">
                        <img src="{{ $branding['logo'] }}" alt="{{ $branding['name'] }}" class="h-12 w-auto">
                    </div>
                @endif

                @if ($questionnaire->bannerUrl())
                    <div class="mb-4 rounded-xl overflow-hidden">
                        <img src="{{ $questionnaire->bannerUrl() }}" alt="" class="w-full object-cover max-h-48">
                    </div>
                @endif

                @yield('content')

                @if ($branding['name'])
                    <p class="mt-8 text-center text-xs text-gray-400">{{ $branding['name'] }}</p>
                @endif
            </div>
        </div>
    </body>
</html>
