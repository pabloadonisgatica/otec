<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\Instructor;
use App\Models\CourseContent;


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
        'company_id' => ['required', 'exists:companies,id'],
        'sence_code' => ['nullable', 'string', 'max:255'],
        'name' => ['required', 'string', 'max:255'],

        'hours' => ['nullable', 'integer', 'min:0'],
        'start_date' => ['nullable', 'date'],
        'end_date' => ['nullable', 'date'],
        'status' => ['required', 'string'],

        // SENCE
        'activity_type' => ['nullable', 'in:curso,seminario'],
        'instruction_modality' => ['nullable', 'in:presencial,elearning_sync,elearning_async,distance_self'],
        'attendance_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
        'min_grade' => ['nullable', 'numeric', 'min:0'],
        'min_hours' => ['nullable', 'integer', 'min:0'],

        'participants_count' => ['nullable', 'integer', 'min:0'],
        'value_per_participant' => ['nullable', 'integer', 'min:0'],

        'sence_request_date' => ['nullable', 'date'],
        'sence_expiration_date' => ['nullable', 'date'],

        'technical_foundation' => ['nullable', 'string'],
        'target_population' => ['nullable', 'string'],
        'general_objectives' => ['nullable', 'string'],
        'teaching_methodology' => ['nullable', 'string'],

        'notes' => ['nullable', 'string'],

        // Relatores
        'instructor_ids' => ['nullable', 'array'],
        'instructor_ids.*' => ['integer', 'exists:instructors,id'],

        // Contenidos
        'contents' => ['nullable', 'array'],
        'contents.*.activity' => ['nullable', 'string'],
        'contents.*.content' => ['nullable', 'string'],
        'contents.*.hours_theoretical' => ['nullable', 'integer', 'min:0'],
        'contents.*.hours_practical' => ['nullable', 'integer', 'min:0'],
        'contents.*.hours_elearning' => ['nullable', 'integer', 'min:0'],
    ]);

    // 1) Crear curso
    $course = Course::create($data);

    // 2) Relatores
    $course->instructors()->sync($request->input('instructor_ids', []));

    // 3) Contenidos
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
        ->with('status', 'Curso creado.');
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
        'company_id' => ['required', 'exists:companies,id'],
        'sence_code' => ['nullable', 'string', 'max:255'],
        'name' => ['required', 'string', 'max:255'],

        'hours' => ['nullable', 'integer', 'min:0'],
        'start_date' => ['nullable', 'date'],
        'end_date' => ['nullable', 'date'],
        'status' => ['required', 'string'],

        // SENCE
        'activity_type' => ['nullable', 'in:curso,seminario'],
        'instruction_modality' => ['nullable', 'in:presencial,elearning_sync,elearning_async,distance_self'],
        'attendance_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
        'min_grade' => ['nullable', 'numeric', 'min:0'],
        'min_hours' => ['nullable', 'integer', 'min:0'],

        'participants_count' => ['nullable', 'integer', 'min:0'],
        'value_per_participant' => ['nullable', 'integer', 'min:0'],

        'sence_request_date' => ['nullable', 'date'],
        'sence_expiration_date' => ['nullable', 'date'],

        'technical_foundation' => ['nullable', 'string'],
        'target_population' => ['nullable', 'string'],
        'general_objectives' => ['nullable', 'string'],
        'teaching_methodology' => ['nullable', 'string'],

        'notes' => ['nullable', 'string'],

        // Relatores
        'instructor_ids' => ['nullable', 'array'],
        'instructor_ids.*' => ['integer', 'exists:instructors,id'],

        // Contenidos
        'contents' => ['nullable', 'array'],
        'contents.*.activity' => ['nullable', 'string'],
        'contents.*.content' => ['nullable', 'string'],
        'contents.*.hours_theoretical' => ['nullable', 'integer', 'min:0'],
        'contents.*.hours_practical' => ['nullable', 'integer', 'min:0'],
        'contents.*.hours_elearning' => ['nullable', 'integer', 'min:0'],
    ]);

    // 1) Actualizar curso
    $course->update($data);

    // 2) Relatores
    $course->instructors()->sync($request->input('instructor_ids', []));

    // 3) Contenidos: estrategia simple y segura (recrear)
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
