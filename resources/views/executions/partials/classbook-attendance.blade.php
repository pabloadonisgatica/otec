@php
    $locked = $execution->isFinalized();
@endphp

@if($execution->participants->isEmpty())

    <x-ui.empty-state
        title="Sin participantes"
        message="Agrega participantes desde la pestaña Participantes antes de registrar asistencia." />

@elseif($execution->sessions->isEmpty())

    <x-ui.empty-state
        title="Sin agenda generada"
        message="Genera la agenda desde la pestaña Planificación antes de registrar asistencia." />

@else

    @if($locked)

        <div class="rounded-lg border border-gray-300 bg-gray-50 p-4 mb-6">
            <p class="font-semibold text-gray-700">
                🔒 Ejecución finalizada
            </p>
            <p class="text-sm text-gray-500 mt-1">
                Reábrela desde el botón "Reabrir ejecución" para poder editar la asistencia.
            </p>
        </div>

    @endif

    <form
        method="POST"
        action="{{ route('executions.classbook.attendance.update', $execution) }}">

        @csrf
        @method('PUT')

        <fieldset @disabled($locked)>

        <div class="overflow-x-auto border border-gray-200 rounded-xl">

            <table class="min-w-full text-sm">

                <thead>

                    <tr class="bg-gray-50">

                        <th class="text-left p-3 sticky left-0 bg-gray-50 whitespace-nowrap">
                            Participante
                        </th>

                        @foreach($execution->sessions->sortBy('session_date') as $session)

                            <th class="p-3 text-center whitespace-nowrap font-medium text-gray-600">

                                {{ \Carbon\Carbon::parse($session->session_date)->format('d-m-Y') }}

                                @if($session->start_time)

                                    <br>
                                    <span class="text-xs font-normal text-gray-400">
                                        {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}
                                    </span>

                                @endif

                            </th>

                        @endforeach

                    </tr>

                </thead>

                <tbody>

                    @foreach($execution->participants as $participant)

                        <tr class="border-t border-gray-100">

                            <td class="p-3 sticky left-0 bg-white whitespace-nowrap">

                                <p class="font-medium text-gray-900">
                                    {{ $participant->first_name }} {{ $participant->last_name }}
                                </p>

                                <p class="text-xs text-gray-500">
                                    {{ $participant->rut }}
                                </p>

                            </td>

                            @foreach($execution->sessions->sortBy('session_date') as $session)

                                @php
                                    $isPresent = $session->attendances
                                        ->firstWhere('participant_id', $participant->id)
                                        ?->present ?? false;
                                @endphp

                                <td class="p-3 text-center">

                                    <input
                                        type="checkbox"
                                        name="attendance[{{ $session->id }}][]"
                                        value="{{ $participant->id }}"
                                        @checked($isPresent)
                                        class="h-5 w-5 rounded border-gray-300 text-indigo-600">

                                </td>

                            @endforeach

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

        <p class="text-sm text-gray-500 mt-4">
            Marca a los participantes presentes en cada sesión. Los que queden sin marcar se registrarán como ausentes.
        </p>

        <div class="mt-4 flex justify-end">

            <button
                type="submit"
                class="px-5 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">

                Guardar asistencia

            </button>

        </div>

        </fieldset>

    </form>

@endif
