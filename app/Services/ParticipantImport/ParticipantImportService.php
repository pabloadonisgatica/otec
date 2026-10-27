<?php

namespace App\Services\ParticipantImport;

use App\Models\Participant;
use App\Support\Rut;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Carga masiva de participantes en 3 pasos: subir → asignar columnas → revisar.
 * El archivo queda en el disco privado hasta confirmar; no se guarda nada
 * en la base de datos antes de la confirmación.
 */
class ParticipantImportService
{
    private const DIRECTORY = 'imports/participants';

    /** Orden en la revisión: primero lo que necesita atención. */
    private const STATUS_ORDER = ['error' => 0, 'repeated' => 1, 'existing' => 2, 'new' => 3];

    public function __construct(private ImportFileReader $reader)
    {
    }

    public function store(UploadedFile $file): string
    {
        $this->pruneOldFiles();

        $extension = strtolower($file->getClientOriginalExtension() ?: 'csv');

        return $file->storeAs(self::DIRECTORY, Str::uuid() . '.' . $extension, 'local');
    }

    public function read(string $path): array
    {
        if (! Storage::disk('local')->exists($path)) {
            throw new ImportFileException('El archivo de la carga ya no está disponible.');
        }

        return $this->reader->read(Storage::disk('local')->path($path));
    }

    public function discard(string $path): void
    {
        Storage::disk('local')->delete($path);
    }

    public function suggestMapping(array $headers): array
    {
        return ParticipantImportFields::suggest($headers);
    }

    /**
     * Valida cada fila sin guardar nada.
     *
     * @param  string[]  $mapping  índice de columna => campo
     * @return array{rows: array, counts: array<string, int>}
     */
    public function review(string $path, array $mapping): array
    {
        $file = $this->read($path);

        $existing = Participant::query()
            ->pluck('rut')
            ->mapWithKeys(fn ($rut) => [Rut::clean($rut) => true]);

        $seen = [];
        $rows = [];

        foreach ($file['rows'] as $row) {
            $item = $this->buildRow($row, $mapping);

            if ($item['errors'] === []) {
                $key = Rut::clean($item['data']['rut']);

                if (isset($existing[$key])) {
                    $item['status'] = 'existing';
                    $item['errors'][] = 'Ya existe en el sistema.';
                } elseif (isset($seen[$key])) {
                    $item['status'] = 'repeated';
                    $item['errors'][] = "RUT repetido en el archivo (fila {$seen[$key]}).";
                } else {
                    $seen[$key] = $row['line'];
                }
            }

            $rows[] = $item;
        }

        usort($rows, fn ($a, $b) => [self::STATUS_ORDER[$a['status']], $a['line']]
            <=> [self::STATUS_ORDER[$b['status']], $b['line']]);

        $counts = array_merge(
            array_fill_keys(array_keys(self::STATUS_ORDER), 0),
            array_count_values(array_column($rows, 'status'))
        );

        return ['rows' => $rows, 'counts' => $counts];
    }

    /**
     * Crea los participantes nuevos de una revisión.
     */
    public function import(array $review, int $companyId): int
    {
        $new = array_filter($review['rows'], fn ($row) => $row['status'] === 'new');

        DB::transaction(function () use ($new, $companyId) {
            foreach ($new as $row) {
                Participant::create($row['data'] + [
                    'company_id' => $companyId,
                    'status' => 'activo',
                ]);
            }
        });

        return count($new);
    }

    private function buildRow(array $row, array $mapping): array
    {
        $value = function (string $field) use ($row, $mapping): string {
            $parts = [];

            foreach ($mapping as $index => $target) {
                if ($target === $field && ($row['values'][$index] ?? '') !== '') {
                    $parts[] = $row['values'][$index];
                }
            }

            return Str::squish(implode(' ', $parts));
        };

        $errors = [];
        $warnings = [];

        $rawRut = $value('rut');
        $rut = Rut::isValid($rawRut) ? Rut::format($rawRut) : $rawRut;

        if ($rawRut === '') {
            $errors[] = 'Falta el RUT.';
        } elseif (! Rut::isValid($rawRut)) {
            $errors[] = 'RUT inválido (revisa el dígito verificador).';
        }

        $firstName = $value('first_name');
        $lastName = $value('last_name');

        if ($firstName === '') {
            $errors[] = 'Faltan los nombres.';
        }

        if ($lastName === '') {
            $errors[] = 'Faltan los apellidos.';
        }

        if (mb_strlen($firstName) > 255 || mb_strlen($lastName) > 255) {
            $errors[] = 'Nombre o apellido demasiado largo.';
        }

        $email = Str::lower($value('email'));

        if ($email !== '' && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $warnings[] = "Correo inválido ({$email}): se importará sin correo.";
            $email = '';
        }

        $phone = $value('phone');

        if (mb_strlen($phone) > 50) {
            $warnings[] = 'Teléfono demasiado largo: se importará sin teléfono.';
            $phone = '';
        }

        return [
            'line' => $row['line'],
            'status' => $errors === [] ? 'new' : 'error',
            'errors' => $errors,
            'warnings' => $warnings,
            'data' => [
                'rut' => $rut,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email ?: null,
                'phone' => $phone ?: null,
            ],
        ];
    }

    /** Borra archivos de cargas abandonadas (más de un día). */
    private function pruneOldFiles(): void
    {
        $disk = Storage::disk('local');
        $limit = now()->subDay()->getTimestamp();

        foreach ($disk->files(self::DIRECTORY) as $file) {
            if ($disk->lastModified($file) < $limit) {
                $disk->delete($file);
            }
        }
    }
}
