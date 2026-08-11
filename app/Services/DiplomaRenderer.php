<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\Diploma;

class DiplomaRenderer
{
    public function render(Diploma $diploma): string
    {
        return $this->renderTemplate($diploma->template->content_html, [
            'code' => $diploma->code,
            'issued_at' => optional($diploma->issued_at)->format('d-m-Y'),
            'qr_path' => $diploma->qr_path,
            'snapshot' => $diploma->snapshot,
        ]);
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
            'qr_path' => null,
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
            '{{qr}}'        => $data['qr_path']
                ? '<img src="' . public_path('storage/' . $data['qr_path']) . '" style="width:100px;height:100px;">'
                : '<div style="width:100px;height:100px;border:1px dashed #ccc;display:inline-block;"></div>',
            '{{otec_logo}}' => $logoPath
                ? '<img src="' . public_path('storage/' . $logoPath) . '" style="height:60px;">'
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
