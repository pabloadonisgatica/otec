<x-ui.section class="mt-6">

    <x-slot:header>
        <x-ui.section-header
            title="Participantes"
            subtitle="{{ $execution->participants->count() }} participantes asociados a esta ejecución" />
    </x-slot:header>

    <div class="flex flex-wrap gap-3 mb-6">

        {{-- Agregar participante --}}
        <form method="POST"
              action="{{ route('executions.participants.store', $execution) }}"
              class="flex flex-wrap gap-3">

            @csrf

            <select
                name="participant_id"
                class="min-w-[320px] border-gray-300 rounded-md shadow-sm"
                required>

                <option value="">
                    Seleccione participante
                </option>

                @foreach($participants as $participant)

                    <option value="{{ $participant->id }}">
                        {{ $participant->first_name }}
                        {{ $participant->last_name }}
                    </option>

                @endforeach

            </select>

            <button
                type="submit"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">

                Agregar

            </button>

        </form>

        {{-- Agregar participantes de la empresa --}}
        <form
            method="POST"
            action="{{ route('executions.participants.company', $execution) }}">

            @csrf

            <button
                type="submit"
                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">

                Agregar participantes de la empresa

            </button>

        </form>

        {{-- Eliminar todos --}}
        @if($execution->participants->count())

            <form
                method="POST"
                action="{{ route('executions.participants.destroy-all', $execution) }}"
                onsubmit="return confirm('¿Eliminar TODOS los participantes de esta ejecución?');">

                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">

                    Quitar todos

                </button>

            </form>

        @endif

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