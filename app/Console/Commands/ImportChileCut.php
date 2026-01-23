<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportChileCut extends Command
{
    protected $signature = 'otec:import-cut
        {--url=https://www.subdere.gov.cl/sites/default/files/documentos/CUT_2018_v04.xls : URL del XLS CUT de SUBDERE}
        {--out=config/chile.php : Ruta de salida del config}';

    protected $description = 'Importa CUT (SUBDERE) y genera config/chile.php con regiones y comunas';

    public function handle(): int
    {
        $url = (string) $this->option('url');
        $out = base_path((string) $this->option('out'));

        $this->info("Descargando CUT desde: {$url}");

        $response = Http::timeout(60)->get($url);

        if (!$response->ok()) {
            $this->error("No se pudo descargar el archivo (HTTP {$response->status()})");
            return self::FAILURE;
        }

        $tmpPath = storage_path('app/cut_tmp.xls');
        file_put_contents($tmpPath, $response->body());

        $this->info("Leyendo XLS...");
        $spreadsheet = IOFactory::load($tmpPath);
        $sheet = $spreadsheet->getActiveSheet();

        // Leemos todas las filas como array
        $rows = $sheet->toArray(null, true, true, true);

        if (count($rows) < 2) {
            $this->error("El XLS no trae filas suficientes.");
            return self::FAILURE;
        }

        // Detectar columnas por encabezado (fila 1)
        $header = $rows[1] ?? [];
        $colRegionName = $this->findColumn($header, 'Nombre Región');
        $colCommuneName = $this->findColumn($header, 'Nombre Comuna');

        if (!$colRegionName || !$colCommuneName) {
            $this->error("No pude detectar columnas. Encabezados encontrados: " . implode(' | ', $header));
            return self::FAILURE;
        }

        $regions = [];

        // Desde fila 2 en adelante
        for ($i = 2; $i <= count($rows); $i++) {
            $r = $rows[$i] ?? [];

            $region = trim((string) ($r[$colRegionName] ?? ''));
            $commune = trim((string) ($r[$colCommuneName] ?? ''));

            if ($region === '' || $commune === '') {
                continue;
            }

            // Normalización suave (mantiene tildes, etc.)
            $region = $this->normalize($region);
            $commune = $this->normalize($commune);

            $regions[$region] ??= [];
            $regions[$region][$commune] = true; // set para evitar duplicados
        }

        // Convertir set -> array y ordenar
        foreach ($regions as $region => $set) {
            $list = array_keys($set);
            sort($list, SORT_LOCALE_STRING);
            $regions[$region] = $list;
        }
        ksort($regions, SORT_LOCALE_STRING);

        $this->info("Generando archivo: {$out}");

        $php = "<?php\n\nreturn [\n    'regions' => " . $this->exportPhpArray($regions, 1) . ",\n];\n";

        @mkdir(dirname($out), 0777, true);
        file_put_contents($out, $php);

        $this->info("Listo ✅ Regiones: " . count($regions));
        $this->info("Ahora corre: php artisan config:clear");

        return self::SUCCESS;
    }

    private function findColumn(array $headerRow, string $expected): ?string
    {
        foreach ($headerRow as $col => $name) {
            if (trim((string)$name) === $expected) {
                return $col; // A, B, C...
            }
        }
        return null;
    }

    private function normalize(string $s): string
    {
        // Quita espacios repetidos y normaliza may/min sin perder tildes
        $s = preg_replace('/\s+/', ' ', trim($s));
        return $s;
    }

    private function exportPhpArray(array $arr, int $indentLevel = 0): string
    {
        $indent = str_repeat('    ', $indentLevel);
        $indent2 = str_repeat('    ', $indentLevel + 1);

        $isAssoc = array_keys($arr) !== range(0, count($arr) - 1);

        $out = "[\n";
        if ($isAssoc) {
            foreach ($arr as $k => $v) {
                $key = var_export($k, true);
                if (is_array($v)) {
                    $out .= "{$indent2}{$key} => " . $this->exportPhpArray($v, $indentLevel + 1) . ",\n";
                } else {
                    $out .= "{$indent2}{$key} => " . var_export($v, true) . ",\n";
                }
            }
        } else {
            foreach ($arr as $v) {
                $out .= "{$indent2}" . var_export($v, true) . ",\n";
            }
        }

        $out .= "{$indent}]";
        return $out;
    }
}
