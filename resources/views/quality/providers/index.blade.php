<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Proveedores</h2>

            <a href="{{ route('quality.providers.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                Nuevo Proveedor
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @include('quality._nav')

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">

                    <form method="GET" action="{{ route('quality.providers.index') }}" class="mb-4">
                        <input
                            type="text"
                            name="q"
                            value="{{ $q }}"
                            placeholder="Buscar por nombre, RUT, categoría o contacto…"
                            class="w-full max-w-md rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </form>

                    <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b bg-gray-50">
                                <th class="py-2 px-3">Nombre</th>
                                <th class="py-2 px-3">RUT</th>
                                <th class="py-2 px-3">Categoría</th>
                                <th class="py-2 px-3">Contacto</th>
                                <th class="py-2 px-3">Teléfono</th>
                                <th class="py-2 px-3">Evaluación</th>
                                <th class="py-2 px-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($providers as $provider)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-3">
                                        <a href="{{ route('quality.providers.show', $provider) }}" class="text-indigo-600 hover:underline font-medium">
                                            {{ $provider->name }}
                                        </a>
                                    </td>
                                    <td class="py-2 px-3 text-gray-600">{{ $provider->rut ?? '—' }}</td>
                                    <td class="py-2 px-3 text-gray-600">{{ $provider->category ?? '—' }}</td>
                                    <td class="py-2 px-3 text-gray-600">{{ $provider->contact_name ?? '—' }}</td>
                                    <td class="py-2 px-3 text-gray-600">{{ $provider->phone ?? $provider->contact_phone ?? '—' }}</td>
                                    <td class="py-2 px-3 whitespace-nowrap">
                                        @if($evaluation = $provider->latestEvaluation)
                                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $evaluation->resultBadge() }}">{{ $evaluation->resultLabel() }}</span>
                                            @if($evaluation->isReevaluationOverdue())
                                                <div class="text-xs text-red-600 mt-1">Reevaluación vencida</div>
                                            @else
                                                <div class="text-xs text-gray-500 mt-1">{{ $evaluation->evaluated_at->format('d-m-Y') }}</div>
                                            @endif
                                        @else
                                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Sin evaluar</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-3 text-right whitespace-nowrap">
                                        <x-ui.row-actions>
                                            <x-ui.row-actions.link :href="route('quality.providers.show', $provider)">Ver</x-ui.row-actions.link>
                                            <x-ui.row-actions.link :href="route('quality.providers.edit', $provider)">Editar</x-ui.row-actions.link>
                                        </x-ui.row-actions>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-6 text-center text-gray-500">
                                        No hay proveedores registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>

                    <div class="mt-4">
                        {{ $providers->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
