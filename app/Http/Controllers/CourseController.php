<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\Instructor;
use App\Models\CourseContent;
use App\Models\CourseFolioSequence;
use Illuminate\Support\Facades\DB;



class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['company', 'instructors', 'contents'])
            ->latest()
            ->paginate(15);

        $breadcrumbs = [
            ['label' => 'Cursos', 'url' => route('courses.index')],
        ];

        return view('courses.index', compact('courses', 'breadcrumbs'));
    }



    public function create()
    {
        $breadcrumbs = [
            ['label' => 'Nuevo', 'url' => route('courses.create')],
        ];

        $companies = Company::orderBy('name')->get(['id', 'name']);
        $instructors = Instructor::orderBy('name')->get(['id', 'name']);

        return view('courses.create', compact('breadcrumbs', 'companies', 'instructors'));
    }



public function store(Request $request)
{
    $data = $request->validate([
        'company_id' => ['nullable', 'exists:companies,id'],

        'course_type' => ['required', 'in:sence,licitacion,privado'],
        'name' => ['required', 'string', 'max:255'],

        'instruction_modalities' => ['nullable', 'array'],
        'instruction_modalities.*' => ['string'],

        'start_date' => ['nullable', 'date'],
        'end_date' => ['nullable', 'date'],

        'sence_approval_date' => ['nullable', 'date'],

        'technical_foundation' => ['nullable', 'string'],
        'target_population' => ['nullable', 'string'],
        'general_objectives' => ['nullable', 'string'],
        'teaching_methodology' => ['nullable', 'string'],
        'notes' => ['nullable', 'string'],

        'hours' => ['nullable', 'numeric'],

        'value_per_participant' => ['nullable', 'integer'],
        'participants_count' => ['nullable', 'integer'],

        'attendance_percentage' => ['nullable', 'numeric'],
        'min_grade' => ['nullable', 'numeric'],

        'status' => ['nullable', 'string'],
        'activity_type' => ['nullable', 'string'],
        'sence_code' => ['nullable', 'string'],

        // ✅ contenidos
        'contents' => ['nullable', 'array'],
        'contents.*.activity' => ['nullable', 'string'],
        'contents.*.content' => ['nullable', 'string'],
        'contents.*.hours_theoretical' => ['nullable', 'numeric', 'min:0'],
        'contents.*.hours_practical' => ['nullable', 'numeric', 'min:0'],
        'contents.*.hours_elearning' => ['nullable', 'numeric', 'min:0'],
    ]);

    // ✅ Tomar contenidos fuera de la transacción
    $contents = $request->input('contents', []);

    $course = DB::transaction(function () use ($data, $contents) {

        $seq = CourseFolioSequence::query()->lockForUpdate()->first();

        if (!$seq) {
            $seq = CourseFolioSequence::create(['last_number' => 0]);
            $seq->refresh();
        }

        $next = (int) $seq->last_number + 1;
        $seq->update(['last_number' => $next]);

        $folio = 'PR-' . str_pad((string) $next, 8, '0', STR_PAD_LEFT);
        $data['folio'] = $folio;

        // 1) Crear curso
        $course = Course::create($data);

        // 2) Guardar contenidos
        foreach ($contents as $i => $row) {
            $course->contents()->create([
                'activity' => $row['activity'] ?? null,
                'content' => $row['content'] ?? null,
                'hours_theoretical' => $row['hours_theoretical'] ?? null,
                'hours_practical' => $row['hours_practical'] ?? null,
                'hours_elearning' => $row['hours_elearning'] ?? null,
                'sort_order' => $i,
            ]);
        }

        return $course;
    });

    return redirect()
        ->route('courses.index')
        ->with('status', 'Curso creado con folio ' . $course->folio);
}



    public function edit(Course $course)
{
        $breadcrumbs = [
            ['label' => 'Cursos', 'url' => route('courses.index')],
            ['label' => 'Editar', 'url' => route('courses.edit', $course)],
        ];

        $companies = Company::orderBy('name')->get(['id', 'name']);
        $instructors = Instructor::orderBy('name')->get(['id', 'name']);

        $course->load('instructors', 'contents');

        return view('courses.edit', compact('course', 'breadcrumbs', 'companies', 'instructors'));
}

public function update(Request $request, Course $course)
{
    $data = $request->validate([
        'company_id' => ['nullable', 'exists:companies,id'],

        'course_type' => ['required', 'in:sence,licitacion,privado'],
        'name' => ['required', 'string', 'max:255'],


        'sence_code' => ['nullable', 'string', 'max:255'],
        'sence_approval_date' => ['nullable', 'date'],


        'hours' => ['nullable', 'numeric', 'min:0'],

        'start_date' => ['nullable', 'date'],
        'end_date' => ['nullable', 'date'],
        'status' => ['nullable', 'string'],

        'activity_type' => ['nullable', 'in:curso,seminario'],

        'instruction_modalities' => ['nullable', 'array'],
        'instruction_modalities.*' => ['in:presencial,elearning_sync,elearning_async,distance_self'],


        'attendance_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
        'min_grade' => ['nullable', 'numeric', 'min:0'],

        'participants_count' => ['nullable', 'integer', 'min:0'],
        'value_per_participant' => ['nullable', 'integer', 'min:0'],

        'technical_foundation' => ['nullable', 'string'],
        'target_population' => ['nullable', 'string'],
        'general_objectives' => ['nullable', 'string'],
        'teaching_methodology' => ['nullable', 'string'],

        'notes' => ['nullable', 'string'],

        'contents' => ['nullable', 'array'],
        'contents.*.activity' => ['nullable', 'string'],
        'contents.*.content' => ['nullable', 'string'],
        'contents.*.hours_theoretical' => ['nullable', 'numeric', 'min:0'],
        'contents.*.hours_practical' => ['nullable', 'numeric', 'min:0'],
        'contents.*.hours_elearning' => ['nullable', 'numeric', 'min:0'],
    ]);
    unset($data['folio']);

    $course->update($data);

    // contenidos: borrar y recrear
    $course->contents()->delete();

    foreach ($request->input('contents', []) as $i => $row) {
        $course->contents()->create([
            'activity' => $row['activity'] ?? null,
            'content' => $row['content'] ?? null,
            'hours_theoretical' => $row['hours_theoretical'] ?? null,
            'hours_practical' => $row['hours_practical'] ?? null,
            'hours_elearning' => $row['hours_elearning'] ?? null,
            'sort_order' => $i,
        ]);
    }

    return redirect()
        ->route('courses.index')
        ->with('status', 'Curso actualizado.');
}



public function destroy(Course $course)
{
    // Si el curso está asociado a presupuestos (tabla pivote budget_courses),
    // primero eliminamos esas relaciones para que MySQL permita borrar el curso.
    $course->budgets()->detach();

    $course->delete();

    return redirect()
        ->route('courses.index')
        ->with('status', 'Curso eliminado.');
}
    // show() lo implementamos después con modal/detalle si aplica
    public function show(Course $course)
    {
        return redirect()->route('courses.index');
    }
}
