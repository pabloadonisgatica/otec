<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

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

            <div class="max-w-2xl mx-auto bg-white shadow-sm rounded-xl border border-gray-200 p-8 text-center">

                <div class="text-4xl mb-4">✅</div>

                <h1 class="text-lg font-semibold text-gray-900">
                    ¡Gracias por tu respuesta!
                </h1>

                <p class="text-sm text-gray-500 mt-2">
                    Ya registramos tu opinión sobre <strong>{{ $execution->course_name }}</strong>.
                </p>

                <p class="text-sm text-gray-500 mt-2">
                    Te enviamos un correo con el resumen de tus respuestas.
                </p>

            </div>

        </div>

    </body>
</html>
