<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Check-List de Sala de Clases</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[
                ['label' => 'Ver Norma', 'url' => route('quality.norm.index')],
                ['label' => 'Check-List de Sala de Clases'],
            ]" />

            <p class="text-sm text-gray-500 mb-4">
                Cada fila es una Ejecución — el link lleva directo a su pestaña "Check-List", sin tener que
                buscarla dentro del listado de Ejecuciones.
            </p>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b bg-gray-50">
                                <th class="py-2 px-3">Ejecución</th>
                                <th class="py-2 px-3">Fechas</th>
                                <th class="py-2 px-3">Estado Check-List</th>
                                <th class="py-2 px-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($executions as $execution)
                                @php
                                    $answered = $execution->checklistResponses->count();
                                @endphp
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-3">
                                        <a href="{{ route('executions.show', [$execution, 'tab' => 'checklist']) }}" class="text-indigo-600 hover:underline font-medium">
                                            {{ $execution->course_name ?? optional($execution->course)->name }}
                                        </a>
                                        <span class="text-xs text-gray-400 block">{{ $execution->internal_code }}</span>
                                    </td>
                                    <td class="py-2 px-3 text-gray-600 whitespace-nowrap">
                                        {{ optional($execution->start_date)->format('d-m-Y') }} — {{ optional($execution->end_date)->format('d-m-Y') }}
                                    </td>
                                    <td class="py-2 px-3 text-gray-600">
                                        @if($answered > 0)
                                            <span class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full">{{ $answered }} ítems respondidos</span>
                                        @else
                                            <span class="text-xs text-gray-400">Sin completar</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-3 text-right">
                                        <a href="{{ route('executions.show', [$execution, 'tab' => 'checklist']) }}" class="text-indigo-600 hover:underline text-xs font-medium">
                                            Abrir Check-List
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-gray-500">
                                        No hay ejecuciones registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $executions->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
