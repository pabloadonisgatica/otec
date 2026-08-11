@php
    $locked = $execution->isFinalized();
@endphp

@if($execution->participants->isEmpty())

    <x-ui.empty-state
        title="Sin participantes"
        message="Agrega participantes desde la pestaña Participantes antes de registrar notas." />

@else

    @if($locked)

        <div class="rounded-lg border border-gray-300 bg-gray-50 p-4 mb-6">
            <p class="font-semibold text-gray-700">
                🔒 Ejecución finalizada
            </p>
            <p class="text-sm text-gray-500 mt-1">
                Reábrela desde el botón "Reabrir ejecución" para poder editar las notas.
            </p>
        </div>

    @endif

    <form
        method="POST"
        action="{{ route('executions.classbook.grades.update', $execution) }}">

        @csrf
        @method('PUT')

        <fieldset @disabled($locked)>

        <div class="divide-y divide-gray-200 border border-gray-200 rounded-xl overflow-hidden">

            @foreach($execution->participants as $participant)

                @php
                    $evaluation = $execution->evaluations
                        ->firstWhere('participant_id', $participant->id);
                @endphp

                <div class="flex items-center justify-between gap-4 p-4">

                    <div>

                        <p class="font-medium text-gray-900">
                            {{ $participant->first_name }} {{ $participant->last_name }}
                        </p>

                        <p class="text-sm text-gray-500">
                            {{ $participant->rut }}
                        </p>

                    </div>

                    <input
                        type="number"
                        step="0.1"
                        min="1"
                        max="7"
                        name="grades[{{ $participant->id }}]"
                        value="{{ old('grades.' . $participant->id, $evaluation?->final_grade) }}"
                        class="w-24 rounded-lg border-gray-300 text-center">

                </div>

            @endforeach

        </div>

        <p class="text-sm text-gray-500 mt-4">
            Escala 1.0 - 7.0. Deja el campo vacío si el participante aún no tiene nota.
        </p>

        <div class="mt-4 flex justify-end">

            <button
                type="submit"
                class="px-5 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">

                Guardar notas

            </button>

        </div>

        </fieldset>

    </form>

@endif
