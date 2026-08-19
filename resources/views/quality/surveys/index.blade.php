<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Encuestas de Evaluación</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @include('quality._nav')

            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-green-50 text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <p class="text-sm text-gray-500 mb-4">
                Vista consolidada de ambos tipos de evaluación de desempeño de Relatores: la que responden los
                participantes (dentro de cada Ejecución) y la que registra la Gerencia. Cada fila es una
                combinación Ejecución + Relator.
            </p>

            {{-- Filtro por tipo --}}
            <div class="flex gap-2 mb-4">
                @foreach(['' => 'Todas', 'participante' => 'Solo Participantes', 'gerencia' => 'Solo Gerencia'] as $value => $label)
                    <a href="{{ route('quality.surveys.index', array_filter(['type' => $value])) }}"
                       class="px-3 py-1.5 rounded-full text-xs font-medium {{ $type == $value ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b bg-gray-50">
                                <th class="py-2 px-3">Ejecución</th>
                                <th class="py-2 px-3">Fechas</th>
                                <th class="py-2 px-3">Relator</th>
                                <th class="py-2 px-3">Tipo</th>
                                <th class="py-2 px-3">Encuesta Participantes</th>
                                <th class="py-2 px-3">Evaluación Gerencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $row)
                                @php
                                    $execution = $row['execution'];
                                    $instructor = $row['instructor'];
                                    $hasParticipant = $row['participant_responses_count'] > 0;
                                    $management = $row['management_evaluation'];
                                @endphp
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-3">
                                        <a href="{{ route('executions.show', $execution) }}" class="text-indigo-600 hover:underline font-medium">
                                            {{ $execution->course_name ?? optional($execution->course)->name }}
                                        </a>
                                        <span class="text-xs text-gray-400 block">{{ $execution->internal_code }}</span>
                                    </td>
                                    <td class="py-2 px-3 text-gray-600 whitespace-nowrap">
                                        {{ optional($execution->start_date)->format('d-m-Y') }} — {{ optional($execution->end_date)->format('d-m-Y') }}
                                    </td>
                                    <td class="py-2 px-3">{{ $instructor->name }}</td>
                                    <td class="py-2 px-3">
                                        @if($hasParticipant)
                                            <span class="inline-block text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full mr-1">Participantes</span>
                                        @endif
                                        @if($management)
                                            <span class="inline-block text-xs bg-purple-50 text-purple-700 px-2 py-0.5 rounded-full">Gerencia</span>
                                        @endif
                                        @if(!$hasParticipant && !$management)
                                            <span class="text-xs text-gray-400">Sin evaluaciones</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-3">
                                        @if($hasParticipant)
                                            <span class="text-gray-600">{{ $row['participant_responses_count'] }} respuestas</span>
                                            <a href="{{ route('executions.show', $execution) }}" class="text-indigo-600 hover:underline text-xs block">Ver resultados</a>
                                        @else
                                            <span class="text-xs text-gray-400">Sin respuestas</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-3">
                                        @if($management)
                                            <a href="{{ route('quality.surveys.management.show', $management) }}" class="text-indigo-600 hover:underline text-xs">
                                                Ver evaluación ({{ $management->submitted_at->format('d-m-Y') }})
                                            </a>
                                        @else
                                            <a href="{{ route('quality.surveys.evaluate.create', [$execution, $instructor]) }}"
                                               class="inline-flex items-center px-2 py-1 rounded-md bg-teal-600 text-white text-xs font-medium hover:bg-teal-700">
                                                Evaluar
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-gray-500">
                                        No hay ejecuciones con relatores asignados todavía.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
