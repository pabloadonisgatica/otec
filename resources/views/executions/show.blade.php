<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Ejecución {{ $execution->internal_code }}
            </h2>

            <div class="flex items-center gap-3">

                @if(in_array($execution->status, ['planificada', 'en_ejecucion']))

                    @php
                        $sessionsWithoutAttendance = $execution->sessions->filter(
                            fn ($session) => $session->attendances->isEmpty()
                        )->count();

                        $hoursIncomplete = $planningValidation['status'] !== 'complete';

                        $closeWarnings = [];

                        if ($hoursIncomplete) {
                            $closeWarnings[] = 'las horas planificadas no están completas';
                        }

                        if ($sessionsWithoutAttendance > 0) {
                            $closeWarnings[] = $sessionsWithoutAttendance . ' sesión(es) sin asistencia registrada';
                        }

                        $closeConfirmMessage = $closeWarnings
                            ? 'Atención: ' . implode(' y ', $closeWarnings) . '. ¿Cerrar la ejecución de todas formas?'
                            : '¿Cerrar esta ejecución?';
                    @endphp

                    <form
                        method="POST"
                        action="{{ route('executions.close', $execution) }}"
                        onsubmit="return confirm('{{ $closeConfirmMessage }}')">

                        @csrf
                        @method('PUT')

                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">

                            Cerrar ejecución

                        </button>

                    </form>

                @elseif($execution->status === 'finalizada')

                    <form
                        method="POST"
                        action="{{ route('executions.reopen', $execution) }}"
                        onsubmit="return confirm('¿Reabrir esta ejecución para poder editarla?')">

                        @csrf
                        @method('PUT')

                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 bg-amber-50 border border-amber-300 rounded-md font-semibold text-xs text-amber-700 uppercase tracking-widest hover:bg-amber-100">

                            Reabrir ejecución

                        </button>

                    </form>

                @endif

                <a href="{{ route('executions.edit', $execution) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                    Editar
                </a>

            </div>
        </div>
    </x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="$breadcrumbs" />
            <div class="border-b border-gray-200 mb-6">

                <nav class="flex gap-8">

                    <a href="{{ route('executions.show', [$execution, 'tab' => 'general']) }}"
                        class="py-3 border-b-2 {{ $tab == 'general'
                ? 'border-indigo-600 text-indigo-600 font-semibold'
                : 'border-transparent text-gray-500 hover:text-gray-700' }}">

                        Información

                    </a>

                    <a href="{{ route('executions.show', [$execution, 'tab' => 'planning']) }}"
                        class="py-3 border-b-2 {{ $tab == 'planning'
                ? 'border-indigo-600 text-indigo-600 font-semibold'
                : 'border-transparent text-gray-500 hover:text-gray-700' }}">

                        Planificación

                    </a>

                    <a href="{{ route('executions.show', [$execution, 'tab' => 'sessions']) }}"
                        class="py-3 border-b-2 {{ $tab == 'sessions'
                ? 'border-indigo-600 text-indigo-600 font-semibold'
                : 'border-transparent text-gray-500 hover:text-gray-700' }}">

                        Agenda

                    </a>

                    <a href="{{ route('executions.show', [$execution, 'tab' => 'classbook']) }}"
                        class="py-3 border-b-2 {{ $tab == 'classbook'
                ? 'border-indigo-600 text-indigo-600 font-semibold'
                : 'border-transparent text-gray-500 hover:text-gray-700' }}">

                        Libro de clases

                    </a>

                    <a href="{{ route('executions.show', [$execution, 'tab' => 'survey']) }}"
                        class="py-3 border-b-2 {{ $tab == 'survey'
                ? 'border-indigo-600 text-indigo-600 font-semibold'
                : 'border-transparent text-gray-500 hover:text-gray-700' }}">

                        Encuesta

                    </a>

                    <a href="{{ route('executions.show', [$execution, 'tab' => 'participants']) }}"
                        class="py-3 border-b-2 {{ $tab == 'participants'
                ? 'border-indigo-600 text-indigo-600 font-semibold'
                : 'border-transparent text-gray-500 hover:text-gray-700' }}">

                        Participantes

                    </a>

                    <a href="{{ route('executions.show', [$execution, 'tab' => 'instructors']) }}"
                        class="py-3 border-b-2 {{ $tab == 'instructors'
                ? 'border-indigo-600 text-indigo-600 font-semibold'
                : 'border-transparent text-gray-500 hover:text-gray-700' }}">

                        Relatores

                    </a>

                </nav>

            </div>

            @if($tab == 'general')

            @include('executions.partials.summary')

            @include('executions.partials.general')

            @endif

            @if($tab == 'planning')

            @include('executions.partials.planning')

            @endif

            @if($tab == 'sessions')

            @include('executions.partials.sessions')

            @endif

            @if($tab == 'classbook')

            @include('executions.partials.classbook')

            @endif

            @if($tab == 'survey')

            @include('executions.partials.survey')

            @endif

            @if($tab == 'participants')

            @include('executions.partials.participants')

            @endif

            @if($tab == 'instructors')

            @include('executions.partials.instructors')

            @endif

        </div>
    </div>


</x-app-layout>