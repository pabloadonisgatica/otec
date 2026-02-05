<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\Instructor;


class BudgetController extends Controller
{
    public function index()
    {
        $budgets = Budget::with('company')
            ->latest()
            ->paginate(10);

        return view('budgets.index', compact('budgets'));
    }

public function create()
{
    $companies = Company::orderBy('name')->get();
    $courses = \App\Models\Course::orderBy('name')->get();
    $instructors = \App\Models\Instructor::orderBy('name')->get(); // ajusta si no existe name

    $breadcrumbs = [
        ['label' => 'Presupuestos', 'url' => route('budgets.index')],
        ['label' => 'Nuevo', 'url' => route('budgets.create')],
    ];

    // Budget “vacío” para que la vista se comporte como edit
    $budget = new \App\Models\Budget();
    $budget->status = 'draft';
    $budget->total_amount = 0;

    // Relaciones vacías para que no reviente nada
    $budget->setRelation('courses', collect());
    $budget->setRelation('sheet', null);
    $budget->setRelation('company', null);

    return view('budgets.create', compact('budget', 'companies', 'courses', 'instructors', 'breadcrumbs'));
}


    public function store(Request $request)
{
    $data = $request->validate([
        'company_id'    => 'required|exists:companies,id',
        'internal_name' => 'required|string|max:255',
    ]);

    $budget = Budget::create([
        ...$data,
        'status'     => 'draft',
        'created_by' => auth()->id(),
    ]);

    // Crear sheet
    $budget->sheet()->create([
        'inputs'  => [],
        'outputs' => [],
    ]);

    // Reutilizamos la lógica de update para guardar cursos + sheet
    $this->update($request, $budget);

    return redirect()
        ->route('budgets.index')
        ->with('success', 'Presupuesto creado correctamente');
}


   public function edit(Budget $budget)
{
    $budget->load(['company', 'courses.course', 'courses.instructor', 'sheet']);

    $breadcrumbs = [
        ['label' => 'Presupuestos', 'url' => route('budgets.index')],
        ['label' => 'Editar #' . $budget->id, 'url' => route('budgets.edit', $budget)],
    ];
$courses = Course::orderBy('name')->get();
$instructors = Instructor::orderBy('name')->get(); // ajusta si tu campo es first_name/last_name

return view('budgets.edit', compact('budget', 'breadcrumbs', 'courses', 'instructors'));

}

    public function update(Request $request, Budget $budget)
    {
        // por ahora solo cabecera
        $data = $request->validate([
            'internal_name' => 'required|string|max:255',
            'company_id'    => 'required|exists:companies,id',
            'status'        => 'required|string',
            'course_code'   => 'nullable|string|max:100',
            'observations'  => 'nullable|string',
            'includes'      => 'nullable|string',
            'excludes'      => 'nullable|string',
        ]);

        $budget->update($data);
        $lines = $request->input('courses', []);

// Normalizar: quitar filas vacías (sin course_id)
$lines = array_values(array_filter($lines, fn($l) => !empty($l['course_id'])));

$keepIds = [];

foreach ($lines as $i => $line) {
    $participants = (int)($line['participants'] ?? 0);
    $hours        = (int)($line['hours'] ?? 0);
    $unitPrice    = (int)($line['unit_price'] ?? 0);
    $discount     = $line['discount_percent'] !== '' ? (int)$line['discount_percent'] : null;

    $lineTotal = $participants * $unitPrice;

    if ($discount !== null && $discount > 0) {
        $lineTotal = (int) round($lineTotal * (1 - ($discount / 100)));
    }

    $payload = [
        'course_id'        => (int)$line['course_id'],
        'participants'     => max(0, $participants),
        'hours'            => max(0, $hours),
        'unit_price'       => max(0, $unitPrice),
        'discount_percent' => $discount,
        'instructor_id'    => !empty($line['instructor_id']) ? (int)$line['instructor_id'] : null,
        'line_total'       => max(0, $lineTotal),
        'sort_order'       => $i + 1,
    ];

    if (!empty($line['id'])) {
        $row = $budget->courses()->whereKey($line['id'])->first();
        if ($row) {
            $row->update($payload);
            $keepIds[] = $row->id;
        }
    } else {
        $row = $budget->courses()->create($payload);
        $keepIds[] = $row->id;
    }
}

// Eliminar líneas quitadas en la UI
$budget->courses()->whereNotIn('id', $keepIds)->delete();
$budget->total_amount = (int) $budget->courses()->sum('line_total');
$budget->save();

        

        // cálculos vendrán después
        return back()->with('success', 'Presupuesto actualizado');

    }

    public function show(Budget $budget)
    {
        // opcional: usar como vista resumen o modal
        return redirect()->route('budgets.edit', $budget);
    }

    public function duplicate(Budget $budget)
    {
        $new = $budget->replicate(['status', 'course_code']);
        $new->status = 'draft';
        $new->created_by = Auth::id();
        $new->save();

        // duplicar cursos
        foreach ($budget->courses as $course) {
            $new->courses()->create($course->toArray());
        }

        // duplicar sheet
        if ($budget->sheet) {
            $new->sheet()->create([
                'inputs'  => $budget->sheet->inputs,
                'outputs' => $budget->sheet->outputs,
            ]);
        }

        return redirect()
            ->route('budgets.edit', $new)
            ->with('status', 'Presupuesto duplicado');
    }

    public function pdf(Budget $budget)
    {
        // placeholder por ahora
        abort(501, 'PDF aún no implementado');
    }
    public function destroy(Budget $budget)
{
    $budget->delete();

    return redirect()
        ->route('budgets.index')
        ->with('success', 'Presupuesto eliminado correctamente');
}


}
