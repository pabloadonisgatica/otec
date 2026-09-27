<?php

namespace Database\Seeders;

use App\Models\DiplomaTemplate;
use Illuminate\Database\Seeder;

class DiplomaTemplateSeeder extends Seeder
{
    public function run(): void
    {
        DiplomaTemplate::updateOrCreate(
            ['name' => 'Diploma Proyecto Humano'],
            [
                'is_active' => true,
                'content_html' => $this->html(),
            ]
        );
    }

    private function html(): string
    {
        return <<<'HTML'
<style>
    @page { margin: 0; }
</style>

<div style="position: relative; width: 11in; height: 8.5in; overflow: hidden;
            font-family: Georgia, 'Times New Roman', serif; color: #1f2937;">

    <div style="position: absolute; top: 0.3in; left: 0.3in; right: 0.3in; bottom: 0.3in;
                border: 2px solid #d97706;"></div>
    <div style="position: absolute; top: 0.42in; left: 0.42in; right: 0.42in; bottom: 0.42in;
                border: 1px solid #f3d9ad;"></div>

    <div style="position: absolute; top: 0.75in; left: 0.9in; right: 0.9in; text-align: center;">

        <div style="margin-bottom: 6px;">{{otec_logo}}</div>

        <div style="font-size: 13px; letter-spacing: 5px; color:#d97706; text-transform:uppercase; font-weight:bold;">
            Proyecto Humano
        </div>
        <div style="font-size: 10px; color:#6b7280; letter-spacing: 1px;">
            Organismo Técnico de Capacitación
        </div>

        <div style="font-size: 34px; letter-spacing: 8px; text-transform:uppercase; margin-top: 30px;">
            Diploma
        </div>
        <div style="width: 90px; height: 2px; background:#d97706; margin: 14px auto 28px;"></div>

        <div style="font-size: 13px; color:#4b5563; margin-bottom: 10px;">
            Se otorga el presente diploma a
        </div>

        <div style="font-size: 32px; font-weight:bold; margin-bottom: 18px;">
            {{participant.full_name}}
        </div>

        <div style="font-size: 13px; color:#4b5563; margin-bottom: 8px;">
            por haber aprobado satisfactoriamente el curso
        </div>

        <div style="font-size: 20px; font-weight:bold; color:#d97706; margin-bottom: 10px;">
            {{course.name}}
        </div>

        <div style="font-size: 11px; color:#6b7280;">
            Realizado entre el {{execution.start_date}} y el {{execution.end_date}}
            &nbsp;—&nbsp; {{course.hours}} horas cronológicas
        </div>
    </div>

    <div style="position: absolute; top: 5.55in; left: 0.9in; width: 9.2in;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width:33%; text-align:center; vertical-align:bottom;">
                <div style="border-top: 1px solid #9ca3af; width:70%; margin: 0 auto 6px;"></div>
                <div style="font-size:10px; color:#6b7280;">Director OTEC</div>
            </td>
            <td style="width:34%; text-align:center; vertical-align:bottom;">
                {{qr}}
                <div style="font-size:9px; color:#6b7280; margin-top:4px;">Código {{code}}</div>
                <div style="font-size:8px; color:#9ca3af;">Verificar en {{validation_url}}</div>
            </td>
            <td style="width:33%; text-align:center; vertical-align:bottom;">
                <div style="border-top: 1px solid #9ca3af; width:70%; margin: 0 auto 6px;"></div>
                <div style="font-size:10px; color:#6b7280;">Emitido el {{issued_at}}</div>
            </td>
        </tr>
    </table>
    </div>

</div>
HTML;
    }
}
