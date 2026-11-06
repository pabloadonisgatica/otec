<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProviderQuoteRequestRequest;
use App\Models\Execution;
use App\Models\Provider;
use App\Models\ProviderQuoteRequest;
use App\Services\ProviderQuoteRequestService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * NCH 2728 — 7.4 Formato de Solicitud de Cotizaciones (compras a proveedores).
 */
class ProviderQuoteRequestController extends Controller
{
    public function __construct(private ProviderQuoteRequestService $service)
    {
    }

    public function index(Request $request)
    {
        $status = $request->string('status')->toString();
        $search = $request->string('q')->toString();
        $providerId = $request->integer('provider_id') ?: null;

        $quoteRequests = ProviderQuoteRequest::query()
            ->with(['provider:id,name', 'execution:id,internal_code,course_name'])
            ->filter($status ?: null, $providerId, $search ?: null)
            ->latest('requested_at')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('quality.quote-requests.index', [
            'quoteRequests' => $quoteRequests,
            'providers' => Provider::orderBy('name')->get(['id', 'name']),
            'status' => $status,
            'search' => $search,
            'providerId' => $providerId,
        ]);
    }

    public function create(Request $request)
    {
        return view('quality.quote-requests.create', [
            'quoteRequest' => new ProviderQuoteRequest([
                'provider_id' => $request->integer('provider_id') ?: null,
                'requested_at' => now(),
                'status' => 'sent',
                'items' => [],
            ]),
            ...$this->formOptions(),
        ]);
    }

    public function store(ProviderQuoteRequestRequest $request)
    {
        $quoteRequest = $this->service->create(
            $request->validated(),
            $request->file('attachment'),
            $request->user()?->id
        );

        return redirect()
            ->route('quality.quote-requests.show', $quoteRequest)
            ->with('status', "Solicitud {$quoteRequest->code} registrada. Descarga el PDF para enviarla al proveedor.");
    }

    public function show(ProviderQuoteRequest $quoteRequest)
    {
        $quoteRequest->load(['provider', 'execution:id,internal_code,course_name', 'requester:id,name']);

        return view('quality.quote-requests.show', [
            'quoteRequest' => $quoteRequest,
            'hasAttachment' => $this->service->attachmentExists($quoteRequest),
        ]);
    }

    public function edit(ProviderQuoteRequest $quoteRequest)
    {
        return view('quality.quote-requests.edit', [
            'quoteRequest' => $quoteRequest,
            ...$this->formOptions(),
        ]);
    }

    public function update(ProviderQuoteRequestRequest $request, ProviderQuoteRequest $quoteRequest)
    {
        $this->service->update(
            $quoteRequest,
            $request->validated(),
            $request->file('attachment'),
            $request->boolean('remove_attachment')
        );

        return redirect()
            ->route('quality.quote-requests.show', $quoteRequest)
            ->with('status', 'Solicitud actualizada.');
    }

    public function destroy(ProviderQuoteRequest $quoteRequest)
    {
        $provider = $quoteRequest->provider_id;
        $this->service->delete($quoteRequest);

        return redirect()
            ->route('quality.providers.show', $provider)
            ->with('status', 'Solicitud de cotización eliminada.');
    }

    public function pdf(ProviderQuoteRequest $quoteRequest)
    {
        $quoteRequest->load(['provider', 'execution:id,internal_code,course_name', 'requester:id,name,email']);

        $html = view('quality.quote-requests.pdf', [
            'quoteRequest' => $quoteRequest,
            ...$this->service->pdfHeader(),
        ])->render();

        return Pdf::loadHTML($html)
            ->setPaper('letter', 'portrait')
            ->stream("solicitud-cotizacion-{$quoteRequest->code}.pdf");
    }

    public function attachment(ProviderQuoteRequest $quoteRequest)
    {
        abort_unless($this->service->attachmentExists($quoteRequest), 404);

        return Storage::disk($this->service->disk())
            ->response($quoteRequest->attachment_path, $quoteRequest->attachment_name);
    }

    private function formOptions(): array
    {
        return [
            'providers' => Provider::orderBy('name')->get(['id', 'name', 'category']),
            'executions' => Execution::orderByDesc('start_date')
                ->get(['id', 'internal_code', 'course_name']),
            'statuses' => ProviderQuoteRequest::STATUSES,
        ];
    }
}
