<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Solicitudes de Cotización</h2>

            <a href="{{ route('quality.quote-requests.create', array_filter(['provider_id' => $providerId])) }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                Nueva solicitud
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @include('quality._nav')

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">

                    <form method="GET" action="{{ route('quality.quote-requests.index') }}"
                          class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 pb-6 border-b border-gray-200">
                        <input type="hidden" name="status" value="{{ $status }}">

                        <div>
                            <label class="block text-xs text-gray-500">Código</label>
                            <input type="text" name="q" value="{{ $search }}" placeholder="Ej: SC-2026-001"
                                   class="mt-1 w-full text-sm rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs text-gray-500">Proveedor</label>
                            <select name="provider_id" class="mt-1 w-full text-sm rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos</option>
                                @foreach($providers as $provider)
                                    <option value="{{ $provider->id }}" @selected($providerId == $provider->id)>{{ $provider->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-end gap-2">
                            <button class="px-4 py-2 rounded-md bg-gray-800 text-white text-xs font-semibold uppercase tracking-widest hover:bg-gray-700">
                                Buscar
                            </button>
                            <a href="{{ route('quality.quote-requests.index') }}" class="px-4 py-2 rounded-md border border-gray-300 text-gray-600 text-xs hover:bg-gray-50">
                                Limpiar
                            </a>
                        </div>
                    </form>

                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach(['' => 'Todas'] + \App\Models\ProviderQuoteRequest::STATUSES as $value => $label)
                            <a href="{{ route('quality.quote-requests.index', array_filter(['status' => $value, 'q' => $search, 'provider_id' => $providerId])) }}"
                               class="px-3 py-1.5 rounded-full text-xs font-medium {{ $status === (string) $value ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>

                    @include('quality.quote-requests._table', ['showProvider' => true])

                    <div class="mt-4">{{ $quoteRequests->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
