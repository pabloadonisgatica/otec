<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; max-width: 560px; margin: 0 auto; padding: 24px;">

    <p>Hola {{ $participant->first_name }},</p>

    <p>
        Gracias por participar en <strong>{{ $execution->course_name }}</strong>.
        Nos gustaría conocer tu opinión sobre la capacitación — la encuesta toma
        solo un par de minutos.
    </p>

    <p style="margin: 32px 0;">
        <a href="{{ $url }}"
           style="background: #4f46e5; color: #ffffff; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: bold;">
            Responder encuesta
        </a>
    </p>

    <p style="font-size: 13px; color: #6b7280;">
        Si el botón no funciona, copia y pega este link en tu navegador:<br>
        {{ $url }}
    </p>

</body>
</html>
