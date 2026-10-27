<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>QR · {{ $questionnaire->name }}</title>
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <style>
            body { font-family: Figtree, sans-serif; margin: 0; color: #111827; background: #fff; }
            .page { min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 24px; padding: 40px; text-align: center; }
            h1 { font-size: 28px; margin: 0; max-width: 700px; }
            .qr svg { width: 360px; height: auto; }
            .hint { font-size: 18px; color: #374151; margin: 0; }
            .url { font-size: 14px; color: #6b7280; word-break: break-all; margin: 0; }
            button { font: inherit; padding: 10px 20px; border-radius: 6px; border: 0; background: #4f46e5; color: #fff; cursor: pointer; }
            @media print { button { display: none; } .page { min-height: auto; padding-top: 80px; } }
        </style>
    </head>
    <body>
        <div class="page">
            <h1>{{ $questionnaire->name }}</h1>
            <div class="qr">{!! $qr !!}</div>
            <p class="hint">Escanea el código con la cámara de tu celular para responder</p>
            <p class="url">{{ $url }}</p>
            <button type="button" onclick="window.print()">Imprimir</button>
        </div>
    </body>
</html>
