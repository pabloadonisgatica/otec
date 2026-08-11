<x-ui.section class="mt-6">

    <x-slot:header>
        <x-ui.section-header
            title="Planificación académica"
            subtitle="Configura la planificación antes de generar la agenda académica." />
    </x-slot:header>

    @php
        $planning = $execution->planning;
        $locked = $execution->isFinalized();
    @endphp

    @if($locked)

        <div class="rounded-lg border border-gray-300 bg-gray-50 p-4 mb-6">
            <p class="font-semibold text-gray-700">
                🔒 Ejecución finalizada
            </p>
            <p class="text-sm text-gray-500 mt-1">
                Reábrela desde el botón "Reabrir ejecución" para poder editar la planificación.
            </p>
        </div>

    @endif

    {{-- Resumen --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <x-ui.stat-card
            title="Horas curso"
            :value="$execution->course_hours" />

        <x-ui.stat-card
            title="Horas por jornada"
            :value="$planning?->hours_per_day ?? '-'" />

        <x-ui.stat-card
            title="Inicio"
            :value="$planning
                ? $planning->start_date->format('d-m-Y')
                : '-'" />

        <x-ui.stat-card
            title="Término"
            :value="$execution->end_date
                ? \Carbon\Carbon::parse($execution->end_date)->format('d-m-Y')
                : '-'" />

    </div>

    <form
        action="{{ route('executions.planning.update',$execution) }}"
        method="POST"
        class="mt-8"
        x-data="{
            mode: '{{ old('mode', $planning?->mode ?? 'automatic') }}',
            shift: '{{ old('shift', $planning?->shift ?? 'morning') }}',
            startTime: '{{ old('start_time', $planning?->start_time ?? '') }}',
            setShift(value) {
                this.shift = value;
                this.startTime = value === 'morning' ? '08:30' : '14:30';
            }
        }">

        @csrf
        @method('PUT')

        <fieldset @disabled($locked)>

        {{-- Modo de planificación --}}
        <div class="mb-8">

            <label class="block text-sm font-medium mb-2">
                Modo de planificación
            </label>

            <div class="grid grid-cols-2 gap-3 max-w-md">

                <label
                    class="flex items-center justify-center gap-2 border rounded-lg p-3 cursor-pointer"
                    :class="mode === 'automatic'
                        ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                        : 'border-gray-300 hover:bg-gray-50'">

                    <input
                        type="radio"
                        name="mode"
                        value="automatic"
                        x-model="mode"
                        class="sr-only">

                    <span>Automático</span>

                </label>

                <label
                    class="flex items-center justify-center gap-2 border rounded-lg p-3 cursor-pointer"
                    :class="mode === 'manual'
                        ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                        : 'border-gray-300 hover:bg-gray-50'">

                    <input
                        type="radio"
                        name="mode"
                        value="manual"
                        x-model="mode"
                        class="sr-only">

                    <span>Manual</span>

                </label>

            </div>

            <p
                class="text-sm text-gray-500 mt-2"
                x-show="mode === 'manual'"
                x-cloak>

                En modo manual la agenda se genera solo con las fechas.
                Deberás definir el horario de cada sesión desde la pestaña Agenda.

            </p>

        </div>

        <div class="grid lg:grid-cols-2 gap-8">

            {{-- Fecha inicio --}}
            <div>

                <label class="block text-sm font-medium mb-2">
                    Fecha de inicio
                </label>

                <input
                    type="date"
                    name="start_date"
                    value="{{ old('start_date', optional($planning?->start_date)->format('Y-m-d') ?? $execution->start_date) }}"
                    class="w-full rounded-lg border-gray-300">

            </div>

            {{-- Jornada --}}
            <div x-show="mode === 'automatic'" x-cloak>

                <label class="block text-sm font-medium mb-2">
                    Jornada
                </label>

                <div class="grid grid-cols-2 gap-3">

                    <label
                        class="flex items-center justify-center gap-2 border rounded-lg p-3 cursor-pointer"
                        :class="shift === 'morning'
                            ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                            : 'border-gray-300 hover:bg-gray-50'"
                        @click="setShift('morning')">

                        <input
                            type="radio"
                            name="shift"
                            value="morning"
                            x-model="shift"
                            :disabled="mode === 'manual'"
                            class="sr-only">

                        <span>Jornada mañana</span>

                    </label>

                    <label
                        class="flex items-center justify-center gap-2 border rounded-lg p-3 cursor-pointer"
                        :class="shift === 'afternoon'
                            ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                            : 'border-gray-300 hover:bg-gray-50'"
                        @click="setShift('afternoon')">

                        <input
                            type="radio"
                            name="shift"
                            value="afternoon"
                            x-model="shift"
                            :disabled="mode === 'manual'"
                            class="sr-only">

                        <span>Jornada tarde</span>

                    </label>

                </div>

            </div>

            {{-- Hora inicio --}}
            <div x-show="mode === 'automatic'" x-cloak>

                <label class="block text-sm font-medium mb-2">
                    Hora de inicio
                </label>

                <input
                    type="time"
                    name="start_time"
                    x-model="startTime"
                    :disabled="mode === 'manual'"
                    class="w-full rounded-lg border-gray-300">

            </div>

            {{-- Horas por jornada --}}
            <div>

                <label class="block text-sm font-medium mb-2">
                    Horas por jornada
                </label>

                <input
                    type="number"
                    min="1"
                    step="0.5"
                    name="hours_per_day"
                    value="{{ old('hours_per_day', $planning?->hours_per_day ?? $execution->hours_per_day) }}"
                    class="w-full rounded-lg border-gray-300">

            </div>

            {{-- Excluir feriados --}}
            <div>

                <label class="block text-sm font-medium mb-3">
                    Opciones
                </label>

                <label class="flex items-center gap-3">

                    <input
                        type="checkbox"
                        name="exclude_holidays"
                        value="1"
                        @checked(old('exclude_holidays', $planning?->exclude_holidays ?? true))

                    >

                    <span>Excluir feriados nacionales</span>

                </label>

            </div>

        </div>

        {{-- Días --}}
        <div class="mt-8">

            <label class="block text-sm font-medium mb-4">

                Días de capacitación

            </label>

            @php

                $selectedDays = old(
                    'week_days',
                    $planning?->week_days ?? [1,2,3,4,5]
                );

                $days = [

                    1 => 'Lunes',
                    2 => 'Martes',
                    3 => 'Miércoles',
                    4 => 'Jueves',
                    5 => 'Viernes',
                    6 => 'Sábado',
                    7 => 'Domingo',

                ];

            @endphp

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">

                @foreach($days as $value => $label)

                    <label
                        class="flex items-center gap-2 border rounded-lg p-3 hover:bg-gray-50">

                        <input
                            type="checkbox"
                            name="week_days[]"
                            value="{{ $value }}"
                            @checked(in_array($value,$selectedDays))

                        >

                        <span>{{ $label }}</span>

                    </label>

                @endforeach

            </div>

        </div>

        <div class="mt-8 flex justify-end gap-3">

            <button
                type="submit"
                class="px-5 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">

                Guardar planificación

            </button>

        </div>

        </fieldset>

    </form>

    <div class="mt-8 border-t pt-8">

        @if($execution->sessions->count())

            <div class="rounded-lg border border-green-200 bg-green-50 p-4">

                <p class="font-semibold text-green-700">

                    Agenda generada

                </p>

                <p class="text-sm text-green-600 mt-1">

                    {{ $execution->sessions->count() }} sesiones programadas.

                </p>

            </div>

        @else

            <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4">

                <p class="font-semibold text-yellow-700">

                    Aún no existe una agenda.

                </p>

            </div>

        @endif

        @unless($planning)

            <p class="text-sm text-gray-500 mt-6 text-right">

                Guarda la planificación antes de generar la agenda.

            </p>

        @endunless

        <form
            action="{{ route('executions.planning.generate',$execution) }}"
            method="POST"
            class="mt-3">

            @csrf

            <div class="flex justify-end">

                <button
                    type="submit"
                    @disabled(!$planning || $locked)
                    class="px-5 py-2 rounded-lg text-white
                        {{ ($planning && !$locked)
                            ? 'bg-indigo-600 hover:bg-indigo-700'
                            : 'bg-gray-300 cursor-not-allowed' }}">

                    Generar agenda

                </button>

            </div>

        </form>

    </div>

</x-ui.section>