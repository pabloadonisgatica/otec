<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Diploma;
use App\Models\DiplomaTemplate;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DiplomaController extends Controller
{
    public function index()
    {
        $diplomas = Diploma::query()
            ->with(['template:id,name', 'course:id,name', 'participant:id,full_name'])
            ->latest()
            ->paginate(20);

        return view('diplomas.index', compact('diplomas'));
    }

    public function create(Request $request)
    {
        $templates = DiplomaTemplate::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $courses = Course::query()
            ->orderByDesc('id')
            ->get(['id', 'name']);

        $selectedCourseId = $request->integer('course_id');
        $participants = collect();

        if ($selectedCourseId) {
            $course = Course::find($selectedCourseId);

            if ($course && method_exists($course, 'participants')) {
                $participants = $course->participants()
                ->orderBy('participants.full_name')
                ->get([
                    'participants.id',
                    'participants.full_name',
                    'participants.rut',
                ]);
            } else {
                // Fallback para que funcione aunque no exista relación curso-participante
                $participants = Participant::query()
                    ->orderBy('full_name')
                    ->get(['id', 'full_name', 'rut']);
            }
        }

        return view('diplomas.create', compact('templates', 'courses', 'participants', 'selectedCourseId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'template_id' => ['required', 'exists:diploma_templates,id'],
            'course_id' => ['required', 'exists:courses,id'],
            'participant_ids' => ['required', 'array', 'min:1'],
            'participant_ids.*' => ['integer', 'exists:participants,id'],
        ]);

        $template = DiplomaTemplate::findOrFail($data['template_id']);
        $course = Course::findOrFail($data['course_id']);

        $participants = Participant::whereIn('id', $data['participant_ids'])->get();

        $created = 0;
        $skipped = 0;

        foreach ($participants as $p) {
            // evitar duplicado por curso+participante
            $exists = Diploma::where('course_id', $course->id)
                ->where('participant_id', $p->id)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            $snapshot = [
                'participant' => [
                    'full_name' => $p->full_name ?? trim(($p->first_name ?? '').' '.($p->last_name ?? '')),
                    'rut' => $p->rut ?? null,
                ],
                'course' => [
                    'name' => $course->name,
                    'hours' => $course->hours ?? null,
                    'start_date' => $course->start_date ?? null,
                    'end_date' => $course->end_date ?? null,
                ],
            ];

            Diploma::create([
                'template_id' => $template->id,
                'course_id' => $course->id,
                'participant_id' => $p->id,
                'code' => $this->uniqueCode(),
                'qr_path' => null,
                'issued_at' => now(),
                'snapshot' => $snapshot,
            ]);

            $created++;
        }

        return redirect()
            ->route('diplomas.index')
            ->with('status', "Diplomas creados: {$created}. Omitidos por duplicado: {$skipped}.");
    }

    private function uniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(10));
        } while (Diploma::where('code', $code)->exists());

        return $code;
    }
}
