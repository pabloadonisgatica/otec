<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\DiplomaLogo;
use App\Models\Execution;
use App\Models\Participant;
use App\Support\DiplomaDates;
use App\Support\Rut;
use Carbon\Carbon;

/**
 * Arma el "snapshot" de un diploma: todos los datos que se imprimen,
 * congelados al momento de emitir. Si después cambia la ejecución,
 * el firmante o un logo, el diploma ya emitido no se altera.
 */
class DiplomaSnapshotBuilder
{
    public function __construct(
        private DiplomaSettingsService $diplomaSettings,
    ) {}

    public function build(Execution $execution, Participant $participant, ?DiplomaLogo $secondaryLogo = null): array
    {
        [$start, $end] = $this->dateRange($execution);
        $settings = $this->diplomaSettings->all();

        return [
            'participant' => [
                'full_name' => trim($participant->first_name . ' ' . $participant->last_name),
                'rut' => Rut::format($participant->rut),
            ],
            'course' => [
                'name' => $execution->course_name,
                'hours' => $execution->course_hours,
            ],
            'execution' => [
                // Se mantienen en d-m-Y por compatibilidad con plantillas HTML antiguas.
                'start_date' => $start?->format('d-m-Y'),
                'end_date' => $end?->format('d-m-Y'),
                'date_phrase' => $start && $end ? DiplomaDates::phrase($start, $end) : null,
                'company' => $execution->company->name ?? null,
            ],
            'otec' => [
                'name' => AppSetting::get('otec_name'),
            ],
            'signer' => [
                'name' => $settings['signer_name'],
                'title' => $settings['signer_title'],
            ],
            'document' => [
                'form_code' => $settings['form_code'],
            ],
            // Rutas en el disco "public" (no se imprimen como texto).
            'assets' => [
                'otec_logo' => AppSetting::get('app_logo'),
                'signature' => $settings['signature_path'],
                'secondary_logo' => $secondaryLogo?->path,
                'secondary_logo_name' => $secondaryLogo?->name,
            ],
        ];
    }

    /**
     * Fechas reales de realización: primera y última sesión.
     * Si la ejecución aún no tiene sesiones, usa sus fechas de inicio/término.
     *
     * @return array{0: ?Carbon, 1: ?Carbon}
     */
    private function dateRange(Execution $execution): array
    {
        $first = $execution->sessions()->min('session_date');
        $last = $execution->sessions()->max('session_date');

        $start = $first ?? $execution->start_date;
        $end = $last ?? $execution->end_date ?? $start;

        return [
            $start ? Carbon::parse($start) : null,
            $end ? Carbon::parse($end) : null,
        ];
    }
}
