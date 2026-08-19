<?php

namespace App\Http\Controllers;

use App\Models\Execution;

class QualityChecklistController extends Controller
{
    public function index()
    {
        $executions = Execution::with(['course', 'checklistResponses'])
            ->orderByDesc('start_date')
            ->paginate(20);

        return view('quality.checklist.index', compact('executions'));
    }
}
