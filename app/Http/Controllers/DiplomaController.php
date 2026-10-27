<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyDiplomasRequest;
use App\Http\Requests\DownloadDiplomasRequest;
use App\Http\Requests\StoreDiplomaRequest;
use App\Models\AppSetting;
use App\Models\Course;
use App\Models\Diploma;
use App\Models\DiplomaLogo;
use App\Models\DiplomaTemplate;
use App\Models\Execution;
use App\Services\DiplomaRenderer;
use App\Services\DiplomaSnapshotBuilder;
use App\Services\DiplomaZipBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class DiplomaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('q')->trim()->toString();
        $courseId = $request->integer('course_id') ?: null;

        $diplomas = Diploma::query()
            ->with([
                'template:id,name',
                'execution:id,course_name,internal_code',
                'participant:id,first_name,last_name,rut',
            ])
            ->listFilters($search ?: null, $courseId)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Solo cursos que tienen diplomas emitidos.
        $courses = Course::query()
            ->whereHas('diplomas')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('diplomas.index', [
            'diplomas' => $diplomas,
            'search' => $search,
            'courses' => $courses,
            'courseId' => $courseId,
            'maxDownload' => DiplomaZipBuilder::MAX_PER_DOWNLOAD,
        ]);
    }

    public function create(Request $request)
    {
        $diplomaLogos = DiplomaLogo::orderBy('name')->get(['id', 'name', 'path']);

        $executions = Execution::query()
            ->orderByDesc('start_date')
            ->get(['id', 'course_name', 'internal_code', 'start_date']);

        $selectedExecutionId = $request->integer('execution_id');
        $participants = collect();
        $alreadyIssuedIds = collect();
        $passedIds = collect();

        if ($selectedExecutionId) {

            $execution = Execution::with(['participants', 'evaluations', 'course'])
                ->find($selectedExecutionId);

            if ($execution) {
                $participants = $execution->participants;

                $alreadyIssuedIds = Diploma::where('execution_id', $execution->id)
                    ->pluck('participant_id');

                $passedIds = $execution->approvedParticipantIds();
            }
        }

        return view('diplomas.create', compact(
            'diplomaLogos',
            'executions',
            'participants',
            'selectedExecutionId',
            'alreadyIssuedIds',
            'passedIds'
        ));
    }

    public function store(StoreDiplomaRequest $request, DiplomaSnapshotBuilder $snapshotBuilder)
    {
        $data = $request->validated();

        // Todos los diplomas nuevos usan el diseño oficial.
        $template = DiplomaTemplate::official();

        $execution = Execution::with(['course', 'company'])
            ->findOrFail($data['execution_id']);

        $secondaryLogo = isset($data['diploma_logo_id'])
            ? DiplomaLogo::find($data['diploma_logo_id'])
            : null;

        $participants = $execution->participants()
            ->whereIn('participants.id', $data['participant_ids'])
            ->get();

        $created = 0;
        $skipped = 0;

        foreach ($participants as $participant) {

            $exists = Diploma::where('execution_id', $execution->id)
                ->where('participant_id', $participant->id)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            Diploma::create([
                'template_id' => $template->id,
                'execution_id' => $execution->id,
                'course_id' => $execution->course_id,
                'participant_id' => $participant->id,
                'code' => $this->uniqueCode(),
                'issued_at' => now(),
                'snapshot' => $snapshotBuilder->build($execution, $participant, $secondaryLogo),
            ]);

            $created++;
        }

        return redirect()
            ->route('diplomas.index')
            ->with('status', "Diplomas creados: {$created}. Omitidos por duplicado: {$skipped}.");
    }

    public function show(Diploma $diploma, DiplomaRenderer $diplomaRenderer)
    {
        $diploma->load(['template', 'execution', 'participant']);

        return view('diplomas.show', [
            'diploma' => $diploma,
            'qrDataUri' => $diplomaRenderer->qrDataUri($diploma->code),
            'validationUrl' => $diplomaRenderer->validationUrl($diploma->code),
        ]);
    }

    public function destroy(Diploma $diploma)
    {
        $this->deleteDiploma($diploma);

        return redirect()
            ->back()
            ->with('status', 'Diploma eliminado. Puedes volver a emitirlo cuando quieras.');
    }

    /**
     * Eliminar varios diplomas seleccionados en el listado.
     */
    public function destroyMany(DestroyDiplomasRequest $request)
    {
        $diplomas = Diploma::whereIn('id', $request->validated('diploma_ids'))->get();

        $diplomas->each(fn (Diploma $diploma) => $this->deleteDiploma($diploma));

        return redirect()
            ->back()
            ->with('status', "Diplomas eliminados: {$diplomas->count()}. Puedes volver a emitirlos cuando quieras.");
    }

    /**
     * Descarga masiva en ZIP: los seleccionados, o todos los del filtro actual.
     */
    public function download(DownloadDiplomasRequest $request, DiplomaZipBuilder $zipBuilder)
    {
        $query = Diploma::query()
            ->with(['template', 'participant', 'execution:id,course_name,internal_code']);

        if ($request->filled('diploma_ids')) {
            $query->whereIn('id', $request->validated('diploma_ids'));
        } else {
            $query->listFilters(
                $request->string('q')->trim()->toString() ?: null,
                $request->integer('course_id') ?: null
            );
        }

        $total = $query->count();

        if ($total === 0) {
            return back()->withErrors(['download' => 'No hay diplomas para descargar.']);
        }

        if ($total > DiplomaZipBuilder::MAX_PER_DOWNLOAD) {
            return back()->withErrors([
                'download' => "Son {$total} diplomas y el máximo por descarga es " . DiplomaZipBuilder::MAX_PER_DOWNLOAD
                    . '. Filtra por curso o selecciona menos.',
            ]);
        }

        $diplomas = $query->get();

        try {
            $path = $zipBuilder->build($diplomas);
        } catch (RuntimeException $e) {
            return back()->withErrors(['download' => $e->getMessage()]);
        }

        return response()
            ->download($path, $zipBuilder->zipName($diplomas))
            ->deleteFileAfterSend();
    }

    private function deleteDiploma(Diploma $diploma): void
    {
        if ($diploma->qr_path) {
            Storage::disk('public')->delete($diploma->qr_path);
        }

        $diploma->delete();
    }

    /**
     * Formulario público para verificar un diploma ingresando su código
     * (para quien tiene el diploma impreso y no escanea el QR). Sin login.
     */
    public function verifyForm(Request $request)
    {
        $code = strtoupper(trim((string) $request->query('code', '')));

        if ($code !== '') {
            return redirect()->route('diplomas.validate', $code);
        }

        return view('diplomas.validate', [
            'diploma' => null,
            'code' => null,
            'otecName' => AppSetting::get('otec_name'),
        ]);
    }

    /**
     * Validación pública del diploma (vía QR o código). Sin login.
     * Expone solo lo mínimo necesario para verificar autenticidad.
     */
    public function validateCode(string $code)
    {
        $code = strtoupper(trim($code));

        $diploma = Diploma::where('code', $code)
            ->with(['execution:id,course_name,start_date,end_date,company_id', 'execution.company:id,name'])
            ->first();

        return view('diplomas.validate', [
            'diploma' => $diploma,
            'code' => $code,
            'otecName' => AppSetting::get('otec_name'),
        ]);
    }

    /**
     * Descargar el diploma en PDF.
     */
    public function pdf(Diploma $diploma, DiplomaRenderer $diplomaRenderer)
    {
        return $diplomaRenderer
            ->toPdf($diplomaRenderer->render($diploma))
            ->stream('diploma-' . $diploma->code . '.pdf');
    }

    private function uniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(10));
        } while (Diploma::where('code', $code)->exists());

        return $code;
    }
}
