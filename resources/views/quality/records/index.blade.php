<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Control de Registros</h2>

            <a href="{{ route('quality.records.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                Crear
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[
                ['label' => 'Ver Norma', 'url' => route('quality.norm.index')],
                ['label' => 'Control de Registros'],
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
                                <th class="py-2 px-3">Nombre Registro</th>
                                <th class="py-2 px-3">Versión</th>
                                <th class="py-2 px-3">Usuario Aprobación</th>
                                <th class="py-2 px-3">Fecha Aprobación</th>
                                <th class="py-2 px-3">Disposición Final</th>
                                <th class="py-2 px-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $record)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-3">
                                        <a href="{{ route('quality.records.show', $record) }}" class="text-indigo-600 hover:underline font-medium">
                                            {{ $record->name }}
                                        </a>
                                    </td>
                                    <td class="py-2 px-3 text-gray-600">v{{ $record->version }}</td>
                                    <td class="py-2 px-3 text-gray-600">{{ $record->approval_user ?? '—' }}</td>
                                    <td class="py-2 px-3 text-gray-600 whitespace-nowrap">{{ optional($record->approval_date)->format('d-m-Y') ?? '—' }}</td>
                                    <td class="py-2 px-3 text-gray-600">{{ $record->final_disposition ?? '—' }}</td>
                                    <td class="py-2 px-3 text-right whitespace-nowrap">
                                        <a href="{{ route('quality.records.show', $record) }}" class="text-indigo-600 hover:underline text-xs font-medium">Ver</a>
                                        <span class="text-gray-300 mx-1">|</span>
                                        <a href="{{ route('quality.records.edit', $record) }}" class="text-gray-600 hover:underline text-xs font-medium">Modificar</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-gray-500">
                                        No hay registros de control creados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $records->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
