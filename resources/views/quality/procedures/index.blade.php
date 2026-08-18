<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Procedimientos</h2>

            <a href="{{ route('quality.procedures.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                Crear
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[
                ['label' => 'Ver Norma', 'url' => route('quality.norm.index')],
                ['label' => 'Procedimientos'],
            ]" />

            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-green-50 text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b bg-gray-50">
                                <th class="py-2 px-3">Nombre</th>
                                <th class="py-2 px-3">Encargado Revisión</th>
                                <th class="py-2 px-3">Fecha Revisión</th>
                                <th class="py-2 px-3">Encargado Aprobación</th>
                                <th class="py-2 px-3">Fecha Aprobación</th>
                                <th class="py-2 px-3">Fecha Próxima Revisión</th>
                                <th class="py-2 px-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($procedures as $procedure)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-3">
                                        <a href="{{ route('quality.procedures.show', $procedure) }}" class="text-indigo-600 hover:underline font-medium">
                                            {{ $procedure->name }}
                                        </a>
                                        <span class="text-xs text-gray-400 block">
                                            v{{ $procedure->versions->first()->version_number ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-3 text-gray-600">{{ $procedure->reviewer_name ?? '—' }}</td>
                                    <td class="py-2 px-3 text-gray-600 whitespace-nowrap">{{ optional($procedure->review_date)->format('d-m-Y') ?? '—' }}</td>
                                    <td class="py-2 px-3 text-gray-600">{{ $procedure->approver_name ?? '—' }}</td>
                                    <td class="py-2 px-3 text-gray-600 whitespace-nowrap">{{ optional($procedure->approval_date)->format('d-m-Y') ?? '—' }}</td>
                                    <td class="py-2 px-3 text-gray-600 whitespace-nowrap">{{ optional($procedure->next_review_date)->format('d-m-Y') ?? '—' }}</td>
                                    <td class="py-2 px-3 text-right whitespace-nowrap">
                                        <a href="{{ route('quality.procedures.show', $procedure) }}" class="text-indigo-600 hover:underline text-xs font-medium">Ver</a>
                                        <span class="text-gray-300 mx-1">|</span>
                                        <a href="{{ route('quality.procedures.edit', $procedure) }}" class="text-gray-600 hover:underline text-xs font-medium">Modificar</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-6 text-center text-gray-500">
                                        No hay procedimientos registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $procedures->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
