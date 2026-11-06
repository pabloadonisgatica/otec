<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $quoteRequest->code }} — {{ $quoteRequest->provider->name }}
            </h2>

            <div class="flex items-center gap-3 shrink-0">
                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $quoteRequest->statusBadge() }}">{{ $quoteRequest->statusLabel() }}</span>

                <a href="{{ route('quality.quote-requests.pdf', $quoteRequest) }}" target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                    Descargar PDF
                </a>

                <a href="{{ route('quality.quote-requests.edit', $quoteRequest) }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-gray-50">
                    Editar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @include('quality._nav')

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800">Solicitud</h3>
                </div>

                <div class="p-6 space-y-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-4 text-sm">
                        <div>
                            <dt class="text-xs text-gray-500">Proveedor</dt>
                            <dd class="mt-0.5">
                                <a href="{{ route('quality.providers.show', $quoteRequest->provider) }}" class="text-indigo-600 hover:underline">{{ $quoteRequest->provider->name }}</a>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Fecha de solicitud</dt>
                            <dd class="mt-0.5 text-gray-800">{{ $quoteRequest->requested_at->format('d-m-Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Responder antes del</dt>
                            <dd class="mt-0.5 text-gray-800">
                                {{ optional($quoteRequest->needed_by)->format('d-m-Y') ?? '—' }}
                                @if($quoteRequest->isOverdue())
                                    <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700">Vencida</span>
                                @endif
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-xs text-gray-500">Ejecución</dt>
                            <dd class="mt-0.5 text-gray-800">
                                {{ $quoteRequest->execution ? $quoteRequest->execution->internal_code.' — '.$quoteRequest->execution->course_name : 'Uso general de la OTEC' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Solicitada por</dt>
                            <dd class="mt-0.5 text-gray-800">{{ $quoteRequest->requester?->name ?? '—' }}</dd>
                        </div>
                    </dl>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left border-b bg-gray-50">
                                    <th class="py-2 px-3 w-10">#</th>
                                    <th class="py-2 px-3">Descripción</th>
                                    <th class="py-2 px-3 text-right">Cantidad</th>
                                    <th class="py-2 px-3">Unidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quoteRequest->items as $item)
                                    <tr class="border-b">
                                        <td class="py-2 px-3 text-gray-500">{{ $loop->iteration }}</td>
                                        <td class="py-2 px-3">{{ $item['description'] }}</td>
                                        <td class="py-2 px-3 text-right">{{ \App\Models\ProviderQuoteRequest::formatQuantity($item['quantity']) }}</td>
                                        <td class="py-2 px-3 text-gray-600">{{ $item['unit'] ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($quoteRequest->conditions)
                        <div>
                            <div class="text-xs text-gray-500">Condiciones</div>
                            <div class="mt-1 text-sm text-gray-800 whitespace-pre-line">{{ $quoteRequest->conditions }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800">Respuesta del proveedor</h3>
                </div>

                <div class="p-6">
                    @if($quoteRequest->hasResponse())
                        <dl class="grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-4 text-sm">
                            <div>
                                <dt class="text-xs text-gray-500">Fecha de respuesta</dt>
                                <dd class="mt-0.5 text-gray-800">{{ optional($quoteRequest->responded_at)->format('d-m-Y') ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500">Monto cotizado</dt>
                                <dd class="mt-0.5 text-gray-800 font-semibold">{{ $quoteRequest->quotedAmountLabel() ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500">Cotización del proveedor</dt>
                                <dd class="mt-0.5">
                                    @if($hasAttachment)
                                        <a href="{{ route('quality.quote-requests.attachment', $quoteRequest) }}" target="_blank" class="text-indigo-600 hover:underline">{{ $quoteRequest->attachment_name }}</a>
                                    @elseif($quoteRequest->attachment_path)
                                        <span class="text-red-600">El archivo no se encuentra en el servidor.</span>
                                    @else
                                        <span class="text-gray-500">Sin archivo</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>

                        @if($quoteRequest->response_notes)
                            <div class="mt-4">
                                <div class="text-xs text-gray-500">Observaciones</div>
                                <div class="mt-1 text-sm text-gray-800 whitespace-pre-line">{{ $quoteRequest->response_notes }}</div>
                            </div>
                        @endif
                    @else
                        <div class="rounded-md bg-amber-50 border border-amber-200 p-4 text-sm text-amber-800">
                            Esperando la cotización del proveedor. Cuando llegue,
                            <a href="{{ route('quality.quote-requests.edit', $quoteRequest) }}" class="font-medium underline">registra la respuesta</a>.
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex items-center justify-between text-xs text-gray-500">
                <a href="{{ route('quality.quote-requests.index') }}" class="text-gray-600 hover:underline">← Todas las solicitudes</a>

                <form method="POST" action="{{ route('quality.quote-requests.destroy', $quoteRequest) }}"
                      onsubmit="return confirm('¿Eliminar la solicitud {{ $quoteRequest->code }}? Esta acción no se puede deshacer.')">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-600 hover:underline">Eliminar solicitud</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
