<?php

namespace App\Http\Controllers;

use App\Models\Execution;
use App\Models\ExecutionChecklistResponse;
use Illuminate\Http\Request;

class ExecutionChecklistController extends Controller
{
    public function update(Request $request, Execution $execution)
    {
        abort_if($execution->isFinalized(), 403);

        $statuses = $request->input('status', []);

        foreach ($statuses as $itemId => $status) {
            if (!in_array($status, ['realizado', 'no_realizado', 'no_aplica'])) {
                continue;
            }

            ExecutionChecklistResponse::updateOrCreate(
                ['execution_id' => $execution->id, 'checklist_item_id' => $itemId],
                ['status' => $status]
            );
        }

        return redirect()
            ->route('executions.show', [$execution, 'tab' => 'checklist'])
            ->with('status', 'Check-List guardado correctamente.');
    }
}
