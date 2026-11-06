<x-ui.section class="mt-6">

    <x-slot:header>
        <x-ui.section-header
            title="Participantes"
            subtitle="{{ $execution->participants->count() }} {{ $execution->participants->count() === 1 ? 'participante asociado' : 'participantes asociados' }} a esta ejecución · Empresa: {{ $execution->company?->name ?? '—' }}" />
    </x-slot:header>

    @php
        $companyName = $execution->company?->name ?? 'La empresa';
        $pending = $availableParticipants->count();
    @endphp

    <div class="mb-6 space-y-4">

        {{-- La empresa aún no tiene participantes: hay que cargarlos primero --}}
        @if ($companyParticipantsCount === 0)
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                <p>
                    <span class="font-semibold">{{ $companyName }}</span> todavía no tiene participantes registrados.
                    Agrégalos a la empresa y luego vuelve aquí para sumarlos a la ejecución.
                </p>
                <div class="mt-3 flex flex-wrap gap-2">
                    <a href="{{ route('participants.import.form', ['company_id' => $execution->company_id]) }}"
                       class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Cargar desde Excel
                    </a>
                    <a href="{{ route('participants.create', ['company_id' => $execution->company_id]) }}"
                       class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                        Crear participante
                    </a>
                </div>
            </div>

        {{-- Todos los de la empresa ya están inscritos --}}
        @elseif ($pending === 0)
            <p class="text-sm text-gray-600">
                {{ $companyParticipantsCount === 1
                    ? "El participante de {$companyName} ya está en esta ejecución."
                    : "Los {$companyParticipantsCount} participantes de {$companyName} ya están en esta ejecución." }}
            </p>
        @endif

        <div class="flex flex-wrap items-center gap-3">

            @if ($pending > 0)
                {{-- Agregar todos los participantes de la empresa (lo habitual) --}}
                <form method="POST" action="{{ route('executions.participants.company', $execution) }}">
                    @csrf
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        {{ $pending === 1 ? 'Agregar 1 participante' : "Agregar los {$pending} participantes" }}
                        de {{ $companyName }}
                    </button>
                </form>

                {{-- Agregar de a uno (solo participantes de la empresa que faltan) --}}
                <form method="POST"
                      action="{{ route('executions.participants.store', $execution) }}"
                      class="flex flex-wrap gap-3">
                    @csrf

                    <select name="participant_id"
                            class="min-w-[320px] border-gray-300 rounded-md shadow-sm"
                            required>
                        <option value="">Seleccione participante</option>
                        @foreach ($availableParticipants as $participant)
                            <option value="{{ $participant->id }}">
                                {{ $participant->first_name }} {{ $participant->last_name }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Agregar
                    </button>
                </form>
            @endif

            {{-- Eliminar todos --}}
            @if ($execution->participants->count())
                <form method="POST"
                      action="{{ route('executions.participants.destroy-all', $execution) }}"
                      onsubmit="return confirm('¿Eliminar TODOS los participantes de esta ejecución?');"
                      class="sm:ml-auto">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Quitar todos
                    </button>
                </form>
            @endif

        </div>

        @error('participant_id')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror

    </div>

    <div class="overflow-x-auto">

        <table class="min-w-full text-sm">

            <thead class="border-b text-left text-gray-600">

                <tr>

                    <th class="py-3 pr-4">
                        Nombre
                    </th>

                    <th class="py-3 pr-4">
                        Correo
                    </th>

                    <th class="py-3 pr-4">
                        Empresa
                    </th>

                    <th class="py-3 text-right">
                        Acciones
                    </th>

                </tr>

            </thead>

            <tbody class="divide-y">

                @forelse($execution->participants as $participant)

                    <tr class="hover:bg-gray-50">

                        <td class="py-3 pr-4">

                            <div class="font-medium text-gray-900">

                                {{ $participant->first_name }}

                                {{ $participant->last_name }}

                            </div>

                        </td>

                        <td class="py-3 pr-4 text-gray-600">

                            {{ $participant->email ?: '—' }}

                        </td>

                        <td class="py-3 pr-4 text-gray-600">

                            {{ $participant->company->name ?? '—' }}

                        </td>

                        <td class="py-3 text-right">

                            <form
                                method="POST"
                                action="{{ route('executions.participants.destroy', [$execution,$participant]) }}"
                                class="inline">

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    onclick="return confirm('¿Quitar participante?')"
                                    class="text-red-600 hover:text-red-800 font-medium">

                                    Quitar

                                </button>

                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="4"
                            class="py-8 text-center text-gray-500">

                            No hay participantes agregados.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</x-ui.section>