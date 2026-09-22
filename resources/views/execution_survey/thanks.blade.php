<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Gracias</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 min-h-screen flex items-center justify-center">
        <div class="max-w-md mx-auto bg-white shadow-sm rounded-xl border border-gray-200 p-8 text-center">
            <h1 class="text-lg font-semibold text-gray-900 mb-2">¡Gracias por tu respuesta!</h1>
            <p class="text-sm text-gray-500">
                Tu opinión sobre {{ $execution->course->name ?? $execution->course_name ?? 'el curso' }}
                fue registrada correctamente.
            </p>
        </div>
    </body>
</html>
