<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\Diploma;
use App\Support\DiplomaDates;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DiplomaRenderer
{
    /**
     * Tamaño único de los diplomas: carta horizontal (11 × 8,5 in).
     * Se usa en la descarga y en la vista previa del editor.
     */
    public const PAPER = 'letter';
    public const ORIENTATION = 'landscape';

    /**
     * Arma el PDF final a partir del HTML ya renderizado.
     */
    public function toPdf(string $content): \Barryvdh\DomPDF\PDF
    {
        // El diseño oficial ya es un documento completo; las plantillas HTML no.
        $html = str_starts_with(ltrim($content), '<!DOCTYPE')
            ? $content
            : $this->wrapDocument($content);

        return Pdf::loadHTML($html)
            ->setPaper(self::PAPER, self::ORIENTATION);
    }

    /**
     * URL pública donde cualquiera puede verificar el diploma.
     */
    public function validationUrl(string $code): string
    {
        return route('diplomas.validate', $code);
    }

    /**
     * QR de validación como imagen SVG embebida (data URI).
     * Se genera al vuelo: no depende de Imagick ni de archivos en disco.
     */
    public function qrDataUri(string $code): string
    {
        $svg = (string) QrCode::format('svg')
            ->size(300)
            ->margin(0)
            ->errorCorrection('M')
            ->generate($this->validationUrl($code));

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    public function render(Diploma $diploma): string
    {
        if ($diploma->template->isOfficial()) {
            return $this->renderOfficial($diploma->snapshot, $diploma->code, $diploma->issued_at ?? now());
        }

        return $this->renderTemplate($diploma->template->content_html, [
            'code' => $diploma->code,
            'issued_at' => optional($diploma->issued_at)->format('d-m-Y'),
            'snapshot' => $diploma->snapshot,
        ]);
    }

    /**
     * Diseño oficial (vista Blade fija). Devuelve el documento HTML
     * completo: no pasa por wrapDocument().
     */
    public function renderOfficial(array $snapshot, string $code, CarbonInterface $issuedAt): string
    {
        File::ensureDirectoryExists(storage_path('fonts'));

        $name = (string) data_get($snapshot, 'participant.full_name', '');
        $course = (string) data_get($snapshot, 'course.name', '');
        $hours = data_get($snapshot, 'course.hours');
        $phrase = data_get($snapshot, 'execution.date_phrase');
        $otecName = data_get($snapshot, 'otec.name') ?? AppSetting::get('otec_name');

        $realization = 'Realizado'
            . ($phrase ? ' ' . $phrase : '')
            . ($hours ? ', con ' . $hours . ' ' . ((int) $hours === 1 ? 'hora' : 'horas') : '')
            . ', impartido por el';

        $secondaryLogo = $this->publicFile(data_get($snapshot, 'assets.secondary_logo'));
        [$secondaryWidth, $secondaryHeight] = $this->fitImage($secondaryLogo, 190, 62);

        return view('diplomas.pdf.official', [
            'fonts' => [
                'regular' => resource_path('fonts/PlusJakartaSans-Regular.ttf'),
                'bold' => resource_path('fonts/PlusJakartaSans-Bold.ttf'),
                'extrabold' => resource_path('fonts/PlusJakartaSans-ExtraBold.ttf'),
            ],
            'frameColor' => '#FF9900',
            'otecLogo' => $this->publicFile(data_get($snapshot, 'assets.otec_logo') ?? AppSetting::get('app_logo')),
            'secondaryLogo' => $secondaryLogo,
            'secondaryLogoWidth' => $secondaryWidth,
            'secondaryLogoHeight' => $secondaryHeight,
            'participantName' => $name,
            'nameSize' => mb_strlen($name) > 38 ? 18 : 22,
            'participantRut' => data_get($snapshot, 'participant.rut'),
            'courseName' => $course,
            'courseSize' => mb_strlen($course) > 95 ? 16 : 20,
            'realizationLine' => $realization,
            'otecLine' => 'OTEC ' . mb_strtoupper((string) $otecName),
            'issuedAt' => DiplomaDates::long($issuedAt),
            'signature' => $this->publicFile(data_get($snapshot, 'assets.signature')),
            'signerName' => data_get($snapshot, 'signer.name'),
            'signerTitle' => data_get($snapshot, 'signer.title'),
            'qr' => $this->qrDataUri($code),
            'code' => $code,
            // Link corto para quien tiene el diploma impreso: "otec.academiaelearning.cl/validar"
            'verifyUrl' => preg_replace('#^https?://#', '', route('diplomas.verify')),
            'formCode' => data_get($snapshot, 'document.form_code'),
        ])->render();
    }

    /**
     * Ruta absoluta de un archivo del disco "public" (DomPDF la lee
     * directo del disco). Null si no existe.
     */
    private function publicFile(?string $path): ?string
    {
        if (! $path || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        return Storage::disk('public')->path($path);
    }

    /**
     * Ancho y alto (pt) para que la imagen quepa en la caja sin deformarse.
     * DomPDF no respeta bien max-width/max-height, por eso se calcula aquí.
     *
     * @return array{0: float, 1: float}
     */
    private function fitImage(?string $file, float $maxWidth, float $maxHeight): array
    {
        $size = $file ? @getimagesize($file) : false;

        if (! $size || $size[0] <= 0 || $size[1] <= 0) {
            return [$maxWidth, $maxHeight];
        }

        $scale = min($maxWidth / $size[0], $maxHeight / $size[1]);

        return [round($size[0] * $scale, 1), round($size[1] * $scale, 1)];
    }

    /**
     * Renderiza el HTML de una plantilla (aún no guardada) con
     * datos de muestra, para la Vista previa del editor.
     */
    public function renderPreview(string $contentHtml): string
    {
        return $this->renderTemplate($contentHtml, [
            'code' => 'PREVIEW-0000000',
            'issued_at' => now()->format('d-m-Y'),
            'snapshot' => [
                'participant' => [
                    'full_name' => 'Juan Pérez González',
                    'rut' => '12.345.678-9',
                ],
                'course' => [
                    'name' => 'Curso de Ejemplo',
                    'hours' => 40,
                ],
                'execution' => [
                    'start_date' => '01-01-2026',
                    'end_date' => '05-01-2026',
                    'company' => 'Empresa de Ejemplo Ltda.',
                ],
            ],
        ]);
    }

    /**
     * Envuelve el HTML del diploma en un documento completo.
     * dompdf no siempre respeta @page cuando recibe HTML suelto
     * (sin <html>/<head>/<body>), así que este es el punto único
     * donde se garantiza que el diploma calce en una sola página.
     */
    public function wrapDocument(string $content): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; }
        body { margin: 0; padding: 0; }
    </style>
</head>
<body>
{$content}
</body>
</html>
HTML;
    }

    private function renderTemplate(string $html, array $data): string
    {
        $logoPath = AppSetting::get('app_logo');

        $replacements = [
            '{{issued_at}}' => $data['issued_at'],
            '{{code}}'      => $data['code'],
            '{{qr}}'        => '<img src="' . $this->qrDataUri($data['code']) . '" style="width:100px;height:100px;">',
            '{{validation_url}}' => e(preg_replace('#^https?://#', '', $this->validationUrl($data['code']))),
            '{{otec_logo}}' => $logoPath
                ? '<img src="' . Storage::disk('public')->path($logoPath) . '" style="height:60px;">'
                : '',
        ];

        foreach ($data['snapshot'] as $group => $values) {
            foreach ($values as $key => $value) {
                $replacements['{{'.$group.'.'.$key.'}}'] = e($value);
            }
        }

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $html
        );
    }
}
