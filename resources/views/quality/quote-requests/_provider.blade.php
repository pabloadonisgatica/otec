{{-- Solicitudes de cotización dentro de la ficha del proveedor (NCH 2728 — 7.4) --}}
@php($quoteRequests = $provider->quoteRequests)

<div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between gap-4">
        <div>
            <h3 class="font-semibold text-gray-800">Solicitudes de cotización</h3>
            <p class="text-xs text-gray-500">NCH 2728 — 7.4. Lo que la OTEC le ha pedido cotizar a este proveedor.</p>
        </div>

        <a href="{{ route('quality.quote-requests.create', ['provider_id' => $provider->id]) }}"
           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500 shrink-0">
            Solicitar cotización
        </a>
    </div>

    <div class="p-6">
        @include('quality.quote-requests._table', ['showProvider' => false])

        @if($quoteRequests->count() === 10)
            <a href="{{ route('quality.quote-requests.index', ['provider_id' => $provider->id]) }}" class="inline-block mt-3 text-sm text-indigo-600 hover:underline">
                Ver todas las solicitudes de este proveedor
            </a>
        @endif
    </div>
</div>
