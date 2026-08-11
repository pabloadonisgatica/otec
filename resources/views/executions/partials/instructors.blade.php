<x-ui.section class="mt-6">

    <x-slot:header>
        <x-ui.section-header
            title="Relatores"
            subtitle="{{ $execution->instructors->count() }} relatores asociados a esta ejecución" />
    </x-slot:header>

    <div class="flex gap-3 mb-6">

        <form
            method="POST"
            action="{{ route('executions.instructors.store', $execution) }}"
            class="flex gap-3">

            @csrf

            <select
                name="instructor_id"
                class="min-w-[320px] border-gray-300 rounded-md shadow-sm"
                required>

                <option value="">
                    Seleccione relator
                </option>

                @foreach($instructors as $instructor)

                    <option value="{{ $instructor->id }}">
                        {{ $instructor->name }}
                    </option>

                @endforeach

            </select>

            <button
                type="submit"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">

                Agregar

            </button>

        </form>

    </div>

    <div class="overflow-x-auto">

        <table class="min-w-full text-sm">

            <thead class="border-b text-left text-gray-600">

                <tr>

                    <th class="py-3 pr-4">
                        Nombre
                    </th>

                    <th class="py-3 pr-4">
                        Profesión
                    </th>

                    <th class="py-3 pr-4">
                        Correo
                    </th>

                    <th class="py-3 text-right">
                        Acciones
                    </th>

                </tr>

            </thead>

            <tbody class="divide-y">

                @forelse($execution->instructors as $instructor)

                    <tr class="hover:bg-gray-50">

                        <td class="py-3 pr-4">

                            <div class="font-medium text-gray-900">
                                {{ $instructor->name }}
                            </div>

                        </td>

                        <td class="py-3 pr-4 text-gray-600">
                            {{ $instructor->profession ?: '—' }}
                        </td>

                        <td class="py-3 pr-4 text-gray-600">
                            {{ $instructor->email ?: '—' }}
                        </td>

                        <td class="py-3 text-right">

                            <form
                                method="POST"
                                action="{{ route('executions.instructors.destroy', [$execution, $instructor]) }}"
                                class="inline">

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    onclick="return confirm('¿Quitar relator?')"
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

                            No hay relatores asociados.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</x-ui.section>