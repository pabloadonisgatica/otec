<?php

namespace App\Http\Controllers;

use App\Http\Requests\MapParticipantImportColumnsRequest;
use App\Http\Requests\UploadParticipantImportRequest;
use App\Models\Company;
use App\Services\ParticipantImport\ImportFileException;
use App\Services\ParticipantImport\ImportFileReader;
use App\Services\ParticipantImport\ParticipantImportFields;
use App\Services\ParticipantImport\ParticipantImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Carga masiva de participantes: subir → columnas → revisar → importar.
 * El estado de cada carga vive en sesión, identificado por un token.
 */
class ParticipantImportController extends Controller
{
    private const SESSION = 'participant_imports';

    public function __construct(private ParticipantImportService $imports)
    {
    }

    public function create()
    {
        return view('participants.import', [
            'companies' => Company::orderBy('name')->get(['id', 'name']),
            'breadcrumbs' => $this->breadcrumbs(),
            'maxRows' => ImportFileReader::MAX_ROWS,
        ]);
    }

    public function upload(UploadParticipantImportRequest $request): RedirectResponse
    {
        $path = $this->imports->store($request->file('file'));

        try {
            $file = $this->imports->read($path);
        } catch (ImportFileException $e) {
            $this->imports->discard($path);

            return back()->withInput()->withErrors(['file' => $e->getMessage()]);
        }

        $token = (string) Str::uuid();

        session()->put(self::SESSION . '.' . $token, [
            'path' => $path,
            'file_name' => $request->file('file')->getClientOriginalName(),
            'company_id' => (int) $request->input('company_id'),
            'mapping' => $this->imports->suggestMapping($file['headers']),
        ]);

        return redirect()->route('participants.import.columns', $token);
    }

    public function columns(string $token)
    {
        if (! $state = $this->state($token)) {
            return $this->expired();
        }

        try {
            $file = $this->imports->read($state['path']);
        } catch (ImportFileException) {
            return $this->expired();
        }

        return view('participants.import.columns', [
            'token' => $token,
            'state' => $state,
            'company' => Company::find($state['company_id']),
            'headers' => $file['headers'],
            'samples' => array_column(array_slice($file['rows'], 0, 2), 'values'),
            'totalRows' => count($file['rows']),
            'mapping' => old('mapping', $state['mapping']),
            'fields' => ParticipantImportFields::FIELDS,
            'required' => ParticipantImportFields::REQUIRED,
            'breadcrumbs' => $this->breadcrumbs(),
        ]);
    }

    public function saveColumns(MapParticipantImportColumnsRequest $request, string $token): RedirectResponse
    {
        if (! $this->state($token)) {
            return $this->expired();
        }

        session()->put(self::SESSION . '.' . $token . '.mapping', $request->mapping());

        return redirect()->route('participants.import.review', $token);
    }

    public function review(string $token)
    {
        if (! $state = $this->state($token)) {
            return $this->expired();
        }

        try {
            $review = $this->imports->review($state['path'], $state['mapping']);
        } catch (ImportFileException) {
            return $this->expired();
        }

        return view('participants.import.review', [
            'token' => $token,
            'state' => $state,
            'company' => Company::find($state['company_id']),
            'review' => $review,
            'breadcrumbs' => $this->breadcrumbs(),
        ]);
    }

    public function confirm(string $token): RedirectResponse
    {
        if (! $state = $this->state($token)) {
            return $this->expired();
        }

        try {
            $review = $this->imports->review($state['path'], $state['mapping']);
        } catch (ImportFileException) {
            return $this->expired();
        }

        $created = $this->imports->import($review, $state['company_id']);

        $this->imports->discard($state['path']);
        session()->forget(self::SESSION . '.' . $token);

        $skipped = count($review['rows']) - $created;

        return redirect()
            ->route('participants.index')
            ->with('success', "Se importaron {$created} participantes." . ($skipped ? " {$skipped} filas no se importaron." : ''));
    }

    /** CSV con las filas que no se importarán y el motivo (abre bien en Excel). */
    public function errors(string $token)
    {
        if (! $state = $this->state($token)) {
            return $this->expired();
        }

        try {
            $review = $this->imports->review($state['path'], $state['mapping']);
        } catch (ImportFileException) {
            return $this->expired();
        }

        $rows = array_filter($review['rows'], fn ($row) => $row['status'] !== 'new');

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Fila', 'RUT', 'Nombres', 'Apellidos', 'Correo', 'Teléfono', 'Motivo'], ';', '"', '');

            foreach ($rows as $row) {
                fputcsv($out, [
                    $row['line'],
                    $row['data']['rut'],
                    $row['data']['first_name'],
                    $row['data']['last_name'],
                    $row['data']['email'],
                    $row['data']['phone'],
                    implode(' ', $row['errors']),
                ], ';', '"', '');
            }

            fclose($out);
        }, 'participantes-no-importados.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Plantilla opcional: cualquier Excel sirve, pero esta ya calza sola. */
    public function template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Participantes');
        $sheet->fromArray([
            ['RUT', 'Nombres', 'Apellido paterno', 'Apellido materno', 'Correo', 'Teléfono'],
            ['12.345.678-5', 'Juan Andrés', 'Pérez', 'González', 'juan.perez@empresa.cl', '+56 9 1234 5678'],
        ]);
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return response()->streamDownload(
            fn () => (new Xlsx($spreadsheet))->save('php://output'),
            'plantilla-participantes.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    private function state(string $token): ?array
    {
        return session(self::SESSION . '.' . $token);
    }

    private function expired(): RedirectResponse
    {
        return redirect()
            ->route('participants.import.form')
            ->with('error', 'La carga expiró o ya fue procesada. Sube el archivo de nuevo.');
    }

    private function breadcrumbs(): array
    {
        return [
            ['label' => 'Participantes', 'url' => route('participants.index')],
            ['label' => 'Carga masiva', 'url' => null],
        ];
    }
}
