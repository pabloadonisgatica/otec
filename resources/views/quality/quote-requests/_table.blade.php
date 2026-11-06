{{-- Tabla de solicitudes de cotización. Se usa en el listado general y en la ficha del proveedor. --}}
<div class="overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead>
            <tr class="text-left border-b bg-gray-50">
                <th class="py-2 px-3">Código</th>
                <th class="py-2 px-3">Solicitud</th>
                @if($showProvider)
                    <th class="py-2 px-3">Proveedor</th>
                @endif
                <th class="py-2 px-3">Ítems</th>
                <th class="py-2 px-3">Ejecución</th>
                <th class="py-2 px-3 text-right">Monto</th>
                <th class="py-2 px-3">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($quoteRequests as $quoteRequest)
                <tr class="border-b hover:bg-gray-50 align-top">
                    <td class="py-2 px-3">
                        <a href="{{ route('quality.quote-requests.show', $quoteRequest) }}" class="font-mono text-indigo-600 hover:underline">{{ $quoteRequest->code }}</a>
                    </td>
                    <td class="py-2 px-3 text-gray-600 whitespace-nowrap">{{ $quoteRequest->requested_at->format('d-m-Y') }}</td>
                    @if($showProvider)
                        <td class="py-2 px-3">{{ $quoteRequest->provider?->name }}</td>
                    @endif
                    <td class="py-2 px-3 text-gray-700">
                        {{ \Illuminate\Support\Str::limit($quoteRequest->items[0]['description'] ?? '', 60) }}
                        @if(count($quoteRequest->items) > 1)
                            <span class="text-xs text-gray-500">+{{ count($quoteRequest->items) - 1 }} más</span>
                        @endif
                    </td>
                    <td class="py-2 px-3 text-gray-600 whitespace-nowrap">{{ $quoteRequest->execution?->internal_code ?? '—' }}</td>
                    <td class="py-2 px-3 text-right whitespace-nowrap">{{ $quoteRequest->quotedAmountLabel() ?? '—' }}</td>
                    <td class="py-2 px-3 whitespace-nowrap">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $quoteRequest->statusBadge() }}">{{ $quoteRequest->statusLabel() }}</span>
                        @if($quoteRequest->isOverdue())
                            <div class="text-xs text-red-600 mt-1">Plazo vencido</div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $showProvider ? 7 : 6 }}" class="py-6 text-center text-gray-500">No hay solicitudes de cotización.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
