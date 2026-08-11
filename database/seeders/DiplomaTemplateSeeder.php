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

<div style="width: 13in; min-height: 8.5in; box-sizing: border-box; padding: 18px;
            font-family: Georgia, 'Times New Roman', serif; color: #1f2937;">

    <div style="width: 100%; min-height: 100%; box-sizing: border-box; padding: 40px 60px;
                border: 2px solid #d97706; position: relative;">

        <div style="position:absolute; top:10px; left:10px; right:10px; bottom:10px;
                    border: 1px solid #f3d9ad;"></div>

        <div style="text-align:center; margin-bottom: 6px;">
            {{otec_logo}}
        </div>

        <div style="text-align:center; margin-bottom: 4px;">
            <div style="font-size: 13px; letter-spacing: 5px; color:#d97706; text-transform:uppercase; font-weight:bold;">
                Proyecto Humano
            </div>
            <div style="font-size: 10px; color:#6b7280; letter-spacing: 1px;">
                Organismo Técnico de Capacitación
            </div>
        </div>

        <div style="text-align:center; margin: 26px 0 14px;">
            <div style="font-size: 32px; letter-spacing: 8px; text-transform:uppercase; color:#1f2937;">
                Diploma
            </div>
            <div style="width: 90px; height: 2px; background:#d97706; margin: 12px auto 0;"></div>
        </div>

        <div style="text-align:center; font-size: 13px; color:#4b5563; margin-bottom: 10px;">
            Se otorga el presente diploma a
        </div>

        <div style="text-align:center; font-size: 28px; font-weight:bold; color:#1f2937; margin-bottom: 14px;">
            {{participant.full_name}}
        </div>

        <div style="text-align:center; font-size: 13px; color:#4b5563; margin: 0 auto 8px; width: 75%;">
            por haber aprobado satisfactoriamente el curso
        </div>

        <div style="text-align:center; font-size: 19px; font-weight:bold; color:#d97706; margin-bottom: 10px;">
            {{course.name}}
        </div>

        <div style="text-align:center; font-size: 11px; color:#6b7280; margin-bottom: 40px;">
            Realizado entre el {{execution.start_date}} y el {{execution.end_date}}
            &nbsp;—&nbsp; {{course.hours}} horas cronológicas
        </div>

        <table style="width:100%; margin-top: 10px;">
            <tr>
                <td style="width:33%; text-align:center; vertical-align:bottom;">
                    <div style="border-top: 1px solid #9ca3af; width:70%; margin: 0 auto 6px;"></div>
                    <div style="font-size:10px; color:#6b7280;">Director OTEC</div>
                </td>
                <td style="width:33%; text-align:center; vertical-align:bottom;">
                    {{qr}}
                    <div style="font-size:9px; color:#9ca3af; margin-top:4px;">
                        Código {{code}}
                    </div>
                </td>
                <td style="width:33%; text-align:center; vertical-align:bottom;">
                    <div style="border-top: 1px solid #9ca3af; width:70%; margin: 0 auto 6px;"></div>
                    <div style="font-size:10px; color:#6b7280;">
                        Emitido el {{issued_at}}
                    </div>
                </td>
            </tr>
        </table>

    </div>

</div>
HTML;
    }
}
