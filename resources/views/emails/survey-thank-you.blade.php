<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; max-width: 560px; margin: 0 auto; padding: 24px;">

    <p>Hola {{ $participant->first_name }},</p>

    <p>
        Gracias por responder la encuesta de <strong>{{ $execution->course_name }}</strong>.
        Este es un resumen de lo que registramos:
    </p>

    <table style="width: 100%; border-collapse: collapse; margin: 16px 0;">
        @foreach($answers as $answer)
            <tr>
                <td style="padding: 6px 8px; border-bottom: 1px solid #e5e7eb; font-size: 13px;">
                    {{ $answer->question->text }}
                </td>
                <td style="padding: 6px 8px; border-bottom: 1px solid #e5e7eb; font-size: 13px; text-align: center; white-space: nowrap;">
                    {{ $answer->score ?? 'N/A' }}
                </td>
            </tr>
        @endforeach
    </table>

    @if($suggestions)
        <p style="font-size: 13px;">
            <strong>Tus comentarios:</strong><br>
            {{ $suggestions }}
        </p>
    @endif

    <p>Agradecemos tu tiempo.</p>

</body>
</html>
