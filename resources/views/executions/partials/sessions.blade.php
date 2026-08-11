<x-ui.section class="mt-6">

    <x-slot:header>
        <x-ui.section-header
            title="Agenda Académica"
            subtitle="Sesiones programadas para esta ejecución" />
    </x-slot:header>

    @php
        $locked = $execution->isFinalized();
    @endphp

    @if($locked)

        <div class="rounded-lg border border-gray-300 bg-gray-50 p-4 mb-6">
            <p class="font-semibold text-gray-700">
                🔒 Ejecución finalizada
            </p>
            <p class="text-sm text-gray-500 mt-1">
                Reábrela desde el botón "Reabrir ejecución" para poder editar la agenda.
            </p>
        </div>

    @endif

    @if($execution->sessions->count())

    <div class=" py-6 flex items-center justify-between">

        <div>

            <div class="text-sm text-gray-500">

                Total sesiones

            </div>

            <div class="font-bold">

                {{ $execution->sessions->count() }}

            </div>

        </div>

        <div>

            <div class="text-sm text-gray-500">

                Total horas

            </div>

            <div class="font-bold">

                {{ $planningValidation['planned_hours'] }}

            </div>

        </div>

        <div>

            @if($planningValidation['status'] === 'incomplete')

                <div class="text-sm text-gray-500">
                    Horas faltantes
                </div>

                <div class="font-bold text-amber-600">
                    {{ $planningValidation['remaining_hours'] }}
                </div>

            @elseif($planningValidation['status'] === 'exceeded')

                <div class="text-sm text-gray-500">
                    Horas excedidas
                </div>

                <div class="font-bold text-red-600">
                    {{ $planningValidation['excess_hours'] }}
                </div>

            @else

                <div class="text-sm text-gray-500">
                    Horas completas
                </div>

                <div class="font-bold text-green-600">
                    ✓
                </div>

            @endif

        </div>

        @if($locked)

            <span class="px-4 py-2 rounded-lg bg-gray-200 text-gray-400 cursor-not-allowed">
                + Agregar sesión
            </span>

        @else

            <a
                href="{{ route('executions.sessions.create', $execution) }}"
                class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">

                + Agregar sesión

            </a>

        @endif

    </div>

    @endif
    @forelse($execution->sessions->sortBy('session_date') as $session)

    <div class="border {{ ($session->start_time && $session->end_time) ? 'border-gray-200 hover:border-indigo-300' : 'border-amber-300' }} rounded-xl p-5 mb-4 transition">

        <div class="flex items-start justify-between">

            <div>

                <h3 class="text-lg font-semibold text-gray-900">
                    {{ \Carbon\Carbon::parse($session->session_date)->translatedFormat('l d \d\e F \d\e Y') }}
                </h3>

                <div class="mt-3 flex items-center gap-6 text-sm text-gray-600">

                    @if($session->start_time && $session->end_time)

                        <div>
                            🕘
                            {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}
                            -
                            {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                        </div>

                    @else

                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                            ⚠️ Falta definir horario
                        </div>

                    @endif

                    <div>
                        ⏱ {{ $session->hours }} horas
                    </div>

                </div>

                <div class="mt-4">

                    @php
                        $attendanceTaken = $session->attendances->isNotEmpty();
                        $presentCount = $session->attendances->where('present', true)->count();
                        $totalParticipants = $execution->participants->count();
                    @endphp

                    <a
                        href="{{ route('executions.show', [$execution, 'tab' => 'classbook']) }}"
                        class="inline-flex items-center gap-2 text-sm font-medium hover:underline
                            {{ $attendanceTaken ? 'text-green-700' : 'text-gray-500' }}">

                        @if($attendanceTaken)

                            ✓ Asistencia: {{ $presentCount }}/{{ $totalParticipants }} presentes

                        @else

                            📋 Asistencia pendiente — ver Libro de clases

                        @endif

                    </a>

                </div>
            </div>

            <div class="flex gap-2">

                @if($locked)

                    <span class="px-3 py-2 rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed">
                        ✏️
                    </span>

                    <span class="px-3 py-2 rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed">
                        🗑
                    </span>

                @else

                    <a
                        href="{{ route('executions.sessions.edit', [$execution, $session]) }}"
                        class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">

                        ✏️

                    </a>

                    <form
                        method="POST"
                        action="{{ route('executions.sessions.destroy', [$execution, $session]) }}"
                        onsubmit="return confirm('¿Eliminar esta sesión?')">

                        @csrf
                        @method('DELETE')

                        <button
                            class="px-3 py-2 rounded-lg border border-red-300 text-red-600 hover:bg-red-50">

                            🗑

                        </button>

                    </form>

                @endif

            </div>

        </div>

    </div>

    @empty

    <x-ui.empty-state
        title="Aún no existe una agenda"
        message="Configura la planificación y genera la agenda desde la pestaña Planificación." />

    @endforelse


</x-ui.section>