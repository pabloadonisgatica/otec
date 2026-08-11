<?php

namespace App\Http\Controllers;

use App\Models\Diploma;
use App\Models\DiplomaTemplate;
use App\Models\Execution;
use App\Services\DiplomaRenderer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DiplomaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('q')->trim()->toString();

        $diplomas = Diploma::query()
            ->with([
                'template:id,name',
                'execution:id,course_name,internal_code',
                'participant:id,first_name,last_name,rut',
            ])
            ->when($search, function ($query, $search) {
                $query->where('code', 'like', "%{$search}%")
                    ->orWhereHas('participant', function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('rut', 'like', "%{$search}%");
                    })
                    ->orWhereHas('execution', function ($q) use ($search) {
                        $q->where('course_name', 'like', "%{$search}%")
                            ->orWhere('internal_code', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('diplomas.index', compact('diplomas', 'search'));
    }

    public function create(Request $request)
    {
        $templates = DiplomaTemplate::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

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

                $minGrade = $execution->course->min_grade ?? 4.0;

                $passedIds = $execution->evaluations
                    ->filter(fn ($evaluation) => $evaluation->final_grade !== null
                        && $evaluation->final_grade >= $minGrade)
                    ->pluck('participant_id');
            }
        }

        return view('diplomas.create', compact(
            'templates',
            'executions',
            'participants',
            'selectedExecutionId',
            'alreadyIssuedIds',
            'passedIds'
        ));
    }

    public function store(Request $request, DiplomaRenderer $diplomaRenderer)
    {
        $data = $request->validate([
            'template_id' => ['required', 'exists:diploma_templates,id'],
            'execution_id' => ['required', 'exists:executions,id'],
            'participant_ids' => ['required', 'array', 'min:1'],
            'participant_ids.*' => ['integer', 'exists:participants,id'],
        ]);

        $template = DiplomaTemplate::findOrFail($data['template_id']);

        $execution = Execution::with(['course', 'company'])
            ->findOrFail($data['execution_id']);

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

            $snapshot = [
                'participant' => [
                    'full_name' => trim($participant->first_name . ' ' . $participant->last_name),
                    'rut' => $participant->rut,
                ],
                'course' => [
                    'name' => $execution->course_name,
                    'hours' => $execution->course_hours,
                ],
                'execution' => [
                    'start_date' => optional($execution->start_date)->format('d-m-Y'),
                    'end_date' => optional($execution->end_date)->format('d-m-Y'),
                    'company' => $execution->company->name ?? null,
                ],
            ];

            $diploma = Diploma::create([
                'template_id' => $template->id,
                'execution_id' => $execution->id,
                'course_id' => $execution->course_id,
                'participant_id' => $participant->id,
                'code' => $this->uniqueCode(),
                'issued_at' => now(),
                'snapshot' => $snapshot,
            ]);

            $this->generateQr($diploma);

            $created++;
        }

        return redirect()
            ->route('diplomas.index')
            ->with('status', "Diplomas creados: {$created}. Omitidos por duplicado: {$skipped}.");
    }

    public function show(Diploma $diploma)
    {
        $diploma->load(['template', 'execution', 'participant']);

        return view('diplomas.show', compact('diploma'));
    }

    public function destroy(Diploma $diploma)
    {
        if ($diploma->qr_path) {
            Storage::disk('public')->delete($diploma->qr_path);
        }

        $diploma->delete();

        return redirect()
            ->route('diplomas.index')
            ->with('status', 'Diploma eliminado. Puedes volver a emitirlo cuando quieras.');
    }

    /**
     * Validación pública del diploma (vía QR). Sin login.
     * Expone solo lo mínimo necesario para verificar autenticidad.
     */
    public function validateCode(string $code)
    {
        $diploma = Diploma::where('code', $code)
            ->with(['execution:id,course_name,start_date,end_date'])
            ->first();

        return view('diplomas.validate', [
            'diploma' => $diploma,
            'code' => $code,
        ]);
    }

    /**
     * Descargar el diploma en PDF.
     */
    public function pdf(Diploma $diploma, DiplomaRenderer $diplomaRenderer)
    {
        $content = $diplomaRenderer->render($diploma);
        $html = $diplomaRenderer->wrapDocument($content);

        $pdf = Pdf::loadHTML($html)->setPaper('folio', 'landscape');

        return $pdf->stream('diploma-' . $diploma->code . '.pdf');
    }

    private function generateQr(Diploma $diploma): void
    {
        $url = route('diplomas.validate', $diploma->code);

        $path = 'diplomas/qr/' . $diploma->code . '.png';

        Storage::disk('public')->put(
            $path,
            QrCode::format('png')->size(300)->generate($url)
        );

        $diploma->update(['qr_path' => $path]);
    }

    private function uniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(10));
        } while (Diploma::where('code', $code)->exists());

        return $code;
    }
}
