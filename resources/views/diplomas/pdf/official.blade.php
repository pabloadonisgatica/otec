<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{--
    Diseño oficial del diploma (carta horizontal, 792 × 612 pt).
    Medidas tomadas del diploma original de Proyecto Humano (F38).
    Todo va en posición absoluta en puntos para que DomPDF lo
    dibuje igual siempre. Los datos llegan listos desde DiplomaRenderer.
--}}
<style>
    @font-face {
        font-family: 'Jakarta';
        font-weight: normal;
        src: url('{{ $fonts['regular'] }}') format('truetype');
    }
    @font-face {
        font-family: 'Jakarta';
        font-weight: bold;
        src: url('{{ $fonts['bold'] }}') format('truetype');
    }
    @font-face {
        font-family: 'JakartaXB';
        font-weight: normal;
        src: url('{{ $fonts['extrabold'] }}') format('truetype');
    }

    @page { margin: 0; size: 792pt 612pt; }
    html, body { margin: 0; padding: 0; }
    body { font-family: 'Jakarta', sans-serif; font-weight: bold; color: #000; }

    .page { position: relative; width: 792pt; height: 612pt; overflow: hidden; }

    .frame-outer {
        position: absolute; left: 46.8pt; top: 43.2pt;
        width: 691pt; height: 511pt;
        border: 3pt solid {{ $frameColor }};
    }
    .frame-inner {
        position: absolute; left: 50pt; top: 46.4pt;
        width: 688.6pt; height: 508.6pt;
        border: 0.8pt solid {{ $frameColor }};
    }

    .row { position: absolute; left: 60pt; width: 672pt; text-align: center; line-height: 1; }

    .logos { position: absolute; left: 60pt; width: 672pt; top: 61.6pt; height: 70.5pt; text-align: center; }
    .logos table { margin: 0 auto; border-collapse: collapse; }
    .logos td { vertical-align: middle; padding: 0; }
    .logo-main { height: 70.5pt; }
    
    .logo-sep { width: 0.8pt; height: 50pt; background: #d1d5db; }

    .title { top: 136.5pt; font-family: 'JakartaXB', sans-serif; font-weight: normal; font-size: 36pt; }
    .small { font-size: 12pt; }
    .course { top: 293.5pt; left: 146pt; width: 500pt; height: 44pt; }
    .course div { line-height: 0.8; }

    .signature { position: absolute; left: 329.75pt; top: 364pt; width: 132.5pt; height: 162pt; }
    .signature-line { position: absolute; left: 338pt; top: 493pt; width: 116pt; height: 0; border-top: 0.6pt solid #000; }
    .signer { position: absolute; left: 196pt; width: 400pt; text-align: center; font-family: Helvetica, Arial, sans-serif; font-weight: bold; font-size: 10.5pt; line-height: 1; }

    .qr { position: absolute; left: 61pt; top: 457pt; width: 76pt; height: 76pt; }
    .qr-code { position: absolute; left: 61pt; top: 536pt; width: 260pt; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 6.5pt; line-height: 1.3; color: #4b5563; }
    .form-code { position: absolute; left: 74pt; top: 569pt; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 7.5pt; }
</style>
</head>
<body>
<div class="page">

    <div class="frame-outer"></div>
    <div class="frame-inner"></div>

    {{-- Logos: OTEC siempre; adicional opcional --}}
    <div class="logos">
        <table>
            <tr>
                @if ($otecLogo)
                    <td><img src="{{ $otecLogo }}" class="logo-main"></td>
                @endif
                @if ($secondaryLogo)
                    <td style="padding: 0 22pt;"><div class="logo-sep"></div></td>
                    <td><img src="{{ $secondaryLogo }}" style="width: {{ $secondaryLogoWidth }}pt; height: {{ $secondaryLogoHeight }}pt;"></td>
                @endif
            </tr>
        </table>
    </div>

    <div class="row title">DIPLOMA</div>

    <div class="row small" style="top: 191.5pt;">Se otorga el presente Diploma a</div>

    <div class="row" style="top: 214.5pt; font-size: {{ $nameSize }}pt;">{{ $participantName }}</div>

    <div class="row" style="top: 246.5pt; font-size: 21pt;">{{ $participantRut }}</div>

    <div class="row small" style="top: 276.5pt;">quien ha participado satisfactoriamente en la actividad de:</div>

    <div class="row course" style="font-size: {{ $courseSize }}pt;"><div>{{ $courseName }}</div></div>

    <div class="row small" style="top: 333.5pt;">{{ $realizationLine }}</div>

    <div class="row" style="top: 354.5pt; font-size: 14pt;">{{ $otecLine }}</div>

    <div class="row" style="top: 380.5pt; font-size: 14pt;">{{ $issuedAt }}</div>

    {{-- Firma --}}
    @if ($signature)
        <img src="{{ $signature }}" class="signature">
    @endif
    <div class="signature-line"></div>
    <div class="signer" style="top: 497pt;">{{ $signerName }}</div>
    <div class="signer" style="top: 510pt;">{{ $signerTitle }}</div>

    {{-- Verificación --}}
    <img src="{{ $qr }}" class="qr">
    <div class="qr-code">Verifica en {{ $verifyUrl }}<br>Código: {{ $code }}</div>

    @if ($formCode)
        <div class="form-code">{{ $formCode }}</div>
    @endif

</div>
</body>
</html>
