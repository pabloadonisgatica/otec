<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequirementIntakeRequest;
use App\Models\Budget;
use App\Models\ClientRequirementIntake;
use App\Models\Company;
use App\Models\User;
use App\Services\ClientRequirementIntakeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * NCH 2728 — 7.2 Levantamiento del Requerimiento del Cliente.
 */
class ClientRequirementIntakeController extends Controller
{
    public function __construct(private ClientRequirementIntakeService $service)
    {
    }

    public function index(Request $request)
    {
        $status = $request->string('status')->toString();
        $search = $request->string('q')->toString();
        $companyId = $request->integer('company_id') ?: null;

        $intakes = ClientRequirementIntake::query()
            ->with(['company:id,name', 'reviewer:id,name', 'budget:id,internal_name'])
            ->filter($status ?: null, $search ?: null, $companyId)
            ->latest('requested_at')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('quality.requirement-intakes.index', [
            'intakes' => $intakes,
            'companies' => Company::orderBy('name')->get(['id', 'name']),
            'status' => $status,
            'search' => $search,
            'companyId' => $companyId,
        ]);
    }

    public function create()
    {
        return view('quality.requirement-intakes.create', [
            'intake' => new ClientRequirementIntake([
                'requested_at' => now(),
                'review_status' => 'pending',
            ]),
            ...$this->formOptions(),
        ]);
    }

    public function store(ClientRequirementIntakeRequest $request)
    {
        $intake = $this->service->create(
            $request->validated(),
            $request->file('attachment'),
            $request->user()?->id
        );

        return redirect()
            ->route('quality.requirement-intakes.show', $intake)
            ->with('status', "Levantamiento {$intake->code} registrado.");
    }

    public function show(ClientRequirementIntake $intake)
    {
        $intake->load(['company', 'reviewer:id,name', 'budget:id,internal_name,budget_date', 'creator:id,name']);

        return view('quality.requirement-intakes.show', [
            'intake' => $intake,
            'hasAttachment' => $this->service->attachmentExists($intake),
        ]);
    }

    public function edit(ClientRequirementIntake $intake)
    {
        return view('quality.requirement-intakes.edit', [
            'intake' => $intake,
            ...$this->formOptions(),
        ]);
    }

    public function update(ClientRequirementIntakeRequest $request, ClientRequirementIntake $intake)
    {
        $this->service->update(
            $intake,
            $request->validated(),
            $request->file('attachment'),
            $request->boolean('remove_attachment')
        );

        return redirect()
            ->route('quality.requirement-intakes.show', $intake)
            ->with('status', 'Levantamiento actualizado.');
    }

    public function destroy(ClientRequirementIntake $intake)
    {
        $this->service->delete($intake);

        return redirect()
            ->route('quality.requirement-intakes.index')
            ->with('status', 'Levantamiento eliminado.');
    }

    public function attachment(ClientRequirementIntake $intake)
    {
        abort_unless($this->service->attachmentExists($intake), 404);

        return Storage::disk($this->service->disk())
            ->response($intake->attachment_path, $intake->attachment_name);
    }

    private function formOptions(): array
    {
        return [
            'companies' => Company::orderBy('name')->get(['id', 'name']),
            'users' => User::orderBy('name')->get(['id', 'name']),
            'budgets' => Budget::with('company:id,name')
                ->orderByDesc('budget_date')
                ->orderByDesc('id')
                ->get(['id', 'company_id', 'internal_name', 'budget_date']),
            'modalities' => ClientRequirementIntake::MODALITIES,
            'reviewStatuses' => ClientRequirementIntake::REVIEW_STATUSES,
        ];
    }
}
