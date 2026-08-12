<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">No Conformidades</h2>

            <a href="{{ route('quality.non-conformities.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                Registrar No Conformidad
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @include('quality._nav')

            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-green-50 text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">

                    {{-- Filtros --}}
                    <form method="GET" action="{{ route('quality.non-conformities.index') }}"
                          class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 pb-6 border-b border-gray-200">

                        <div>
                            <label class="block text-xs text-gray-500">N° Informe (código)</label>
                            <input type="text" name="code" value="{{ $code }}" placeholder="Ej: NC-2026-001"
                                   class="mt-1 w-full text-sm rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500">Detectada desde</label>
                            <input type="date" name="from" value="{{ $from }}"
                                   class="mt-1 w-full text-sm rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500">Detectada hasta</label>
                            <input type="date" name="to" value="{{ $to }}"
                                   class="mt-1 w-full text-sm rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div class="flex items-end gap-2">
                            <button class="px-4 py-2 rounded-md bg-gray-800 text-white text-xs font-semibold uppercase tracking-widest hover:bg-gray-700">
                                Buscar
                            </button>
                            <a href="{{ route('quality.non-conformities.index') }}" class="px-4 py-2 rounded-md border border-gray-300 text-gray-600 text-xs hover:bg-gray-50">
                                Limpiar
                            </a>
                        </div>

                    </form>

                    {{-- Tabs de estado --}}
                    <div class="flex gap-2 mb-4">
                        @foreach(['' => 'Todas', 'open' => 'Abiertas', 'in_progress' => 'En proceso', 'closed' => 'Cerradas'] as $value => $label)
                            <a href="{{ route('quality.non-conformities.index', array_filter(['status' => $value, 'code' => $code, 'from' => $from, 'to' => $to])) }}"
                               class="px-3 py-1.5 rounded-full text-xs font-medium {{ $status == $value ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>

                    <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b bg-gray-50">
                                <th class="py-2 px-3">N° Informe</th>
                                <th class="py-2 px-3">Fecha Detección</th>
                                <th class="py-2 px-3">Título</th>
                                <th class="py-2 px-3">Fuente</th>
                                <th class="py-2 px-3">Fecha Implementación</th>
                                <th class="py-2 px-3">Fecha Verificación</th>
                                <th class="py-2 px-3">Estado</th>
                                <th class="py-2 px-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($nonConformities as $nc)
                                @php
                                    $implementedAt = $nc->actions->max('implemented_at');
                                    $verifiedAt = $nc->actions->max('effectiveness_verified_at');

                                    $sourceLabel = match($nc->source) {
                                        'internal_audit' => 'Auditoría interna',
                                        'external_audit' => 'Auditoría externa',
                                        'complaint' => 'Reclamo',
                                        'survey' => 'Encuesta',
                                        'other' => 'Otra',
                                        default => '—',
                                    };

                                    $badge = match($nc->status) {
                                        'open' => 'bg-red-50 text-red-700',
                                        'in_progress' => 'bg-amber-50 text-amber-700',
                                        'closed' => 'bg-green-50 text-green-700',
                                    };
                                    $statusLabel = match($nc->status) {
                                        'open' => 'Abierta',
                                        'in_progress' => 'En proceso',
                                        'closed' => 'Cerrada',
                                    };
                                @endphp
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-3">
                                        <a href="{{ route('quality.non-conformities.show', $nc) }}" class="font-mono text-indigo-600 hover:underline">
                                            {{ $nc->code }}
                                        </a>
                                    </td>
                                    <td class="py-2 px-3 text-gray-600 whitespace-nowrap">
                                        {{ optional($nc->detected_at)->format('d-m-Y') }}
                                    </td>
                                    <td class="py-2 px-3">{{ $nc->title }}</td>
                                    <td class="py-2 px-3 text-gray-600">{{ $sourceLabel }}</td>
                                    <td class="py-2 px-3 text-gray-600 whitespace-nowrap">
                                        {{ $implementedAt ? \Carbon\Carbon::parse($implementedAt)->format('d-m-Y') : '—' }}
                                    </td>
                                    <td class="py-2 px-3 text-gray-600 whitespace-nowrap">
                                        {{ $verifiedAt ? \Carbon\Carbon::parse($verifiedAt)->format('d-m-Y') : '—' }}
                                    </td>
                                    <td class="py-2 px-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $badge }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-3 text-right whitespace-nowrap">
                                        <a href="{{ route('quality.non-conformities.show', $nc) }}" class="text-indigo-600 hover:underline text-xs font-medium">Ver</a>
                                        <span class="text-gray-300 mx-1">|</span>
                                        <a href="{{ route('quality.non-conformities.edit', $nc) }}" class="text-gray-600 hover:underline text-xs font-medium">Editar</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-6 text-center text-gray-500">
                                        No hay no conformidades registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>

                    <div class="mt-4">
                        {{ $nonConformities->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
