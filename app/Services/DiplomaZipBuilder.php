<?php

namespace App\Services;

use App\Models\Diploma;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;
use ZipArchive;

/**
 * Empaqueta varios diplomas en un ZIP (un PDF por diploma).
 * Los PDF se generan al vuelo con DiplomaRenderer: no hay archivos en disco.
 */
class DiplomaZipBuilder
{
    /**
     * Máximo por descarga. DomPDF tarda ~1-2 s por diploma y el hosting
     * compartido corta las peticiones largas.
     */
    public const MAX_PER_DOWNLOAD = 50;

    public function __construct(private DiplomaRenderer $renderer)
    {
    }

    /**
     * @param  Collection<int, Diploma>  $diplomas
     * @return string Ruta absoluta del ZIP temporal (se borra después de enviarlo).
     */
    public function build(Collection $diplomas): string
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('La extensión zip de PHP no está activa en el servidor.');
        }

        @set_time_limit(300);

        $directory = storage_path('app/tmp');
        File::ensureDirectoryExists($directory);

        $path = $directory . '/diplomas-' . Str::random(16) . '.zip';

        $zip = new ZipArchive();

        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('No se pudo crear el archivo ZIP.');
        }

        $usedNames = [];

        foreach ($diplomas as $diploma) {
            $pdf = $this->renderer->toPdf($this->renderer->render($diploma))->output();

            $zip->addFromString($this->fileName($diploma, $usedNames), $pdf);
        }

        $zip->close();

        return $path;
    }

    /**
     * Nombre del ZIP: incluye la ejecución si todos los diplomas son de la misma.
     */
    public function zipName(Collection $diplomas): string
    {
        $execution = $diplomas->pluck('execution_id')->unique()->count() === 1
            ? $diplomas->first()->execution
            : null;

        $label = $execution ? ($execution->internal_code ?: $execution->course_name) : null;

        return 'diplomas' . ($label ? '-' . Str::slug($label) : '') . '-' . now()->format('Y-m-d') . '.zip';
    }

    /**
     * "12345678-9_Perez_Gonzalez_Juan.pdf". Si el nombre se repite
     * (misma persona en dos ejecuciones) se agrega el código del diploma.
     */
    private function fileName(Diploma $diploma, array &$usedNames): string
    {
        $rut = str_replace('.', '', (string) (data_get($diploma->snapshot, 'participant.rut') ?? $diploma->participant?->rut ?? ''));

        $person = $diploma->participant
            ? $diploma->participant->last_name . ' ' . $diploma->participant->first_name
            : (string) data_get($diploma->snapshot, 'participant.full_name', '');

        $base = collect([$rut, $person])
            ->map(fn ($part) => trim(preg_replace('/[^A-Za-z0-9\-]+/', '_', Str::ascii($part)), '_'))
            ->filter()
            ->implode('_');

        $base = $base !== '' ? $base : 'diploma';

        if (isset($usedNames[$base])) {
            $base .= '_' . $diploma->code;
        }

        $usedNames[$base] = true;

        return $base . '.pdf';
    }
}
