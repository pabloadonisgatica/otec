<?php

namespace App\Services\ParticipantImport;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

/**
 * Lee Excel (.xlsx/.xls) o CSV y devuelve encabezados + filas como texto.
 *
 * El CSV que guarda Excel en Chile usa ";" y codificación Windows-1252:
 * ambos se detectan solos. Las filas vacías se ignoran, pero cada fila
 * conserva su número de línea real para que los errores apunten bien.
 */
class ImportFileReader
{
    public const MAX_ROWS = 1000;

    /**
     * @return array{headers: string[], rows: array<int, array{line: int, values: string[]}>}
     */
    public function read(string $path): array
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        try {
            $matrix = in_array($extension, ['xlsx', 'xls'], true)
                ? $this->readSpreadsheet($path)
                : $this->readCsv($path);
        } catch (Throwable) {
            throw new ImportFileException('No se pudo leer el archivo. Verifica que sea un Excel o CSV válido.');
        }

        // Conserva las claves: clave + 1 = número de línea en el archivo.
        $matrix = array_filter($matrix, fn ($row) => $this->hasValues($row));

        if ($matrix === []) {
            throw new ImportFileException('El archivo no tiene datos.');
        }

        // array_shift reindexaría las claves y perderíamos los números de línea.
        $headerKey = array_key_first($matrix);
        $headers = array_map(fn ($value) => trim((string) $value), array_values($matrix[$headerKey]));
        unset($matrix[$headerKey]);
        $width = count($headers);

        if ($matrix === []) {
            throw new ImportFileException('El archivo solo tiene encabezados, sin participantes.');
        }

        if (count($matrix) > self::MAX_ROWS) {
            throw new ImportFileException('El archivo tiene ' . count($matrix) . ' filas. El máximo por carga es ' . self::MAX_ROWS . '.');
        }

        $rows = [];

        foreach ($matrix as $key => $row) {
            $values = array_pad(array_slice(array_values($row), 0, $width), $width, '');

            $rows[] = [
                'line' => $key + 1,
                'values' => array_map(fn ($value) => trim((string) $value), $values),
            ];
        }

        return $this->dropEmptyColumns($headers, $rows);
    }

    private function readSpreadsheet(string $path): array
    {
        return IOFactory::load($path)->getActiveSheet()->toArray(null, true, true, false);
    }

    private function readCsv(string $path): array
    {
        $content = (string) file_get_contents($path);
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

        if (! mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'Windows-1252');
        }

        $firstLine = strtok($content, "\n") ?: '';
        $delimiter = collect([';', ',', "\t"])
            ->sortByDesc(fn ($candidate) => substr_count($firstLine, $candidate))
            ->first();

        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $content);
        rewind($stream);

        $rows = [];

        while (($row = fgetcsv($stream, 0, $delimiter, '"', '')) !== false) {
            $rows[] = $row;
        }

        fclose($stream);

        return $rows;
    }

    private function hasValues(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return true;
            }
        }

        return false;
    }

    /**
     * Quita columnas sin encabezado y sin datos (típicas al final de un Excel)
     * y nombra las que tienen datos pero no encabezado.
     */
    private function dropEmptyColumns(array $headers, array $rows): array
    {
        $keep = [];

        foreach ($headers as $index => $header) {
            $hasData = collect($rows)->contains(fn ($row) => $row['values'][$index] !== '');

            if ($header !== '' || $hasData) {
                $keep[] = $index;
            }
        }

        return [
            'headers' => array_map(
                fn ($index) => $headers[$index] !== '' ? $headers[$index] : 'Columna ' . ($index + 1),
                $keep
            ),
            'rows' => array_map(fn ($row) => [
                'line' => $row['line'],
                'values' => array_map(fn ($index) => $row['values'][$index], $keep),
            ], $rows),
        ];
    }
}
