<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Ejecuciones
            </h2>

            <a href="{{ route('executions.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Nueva ejecución
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="$breadcrumbs ?? []" />

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-left text-gray-600 border-b">
                                <tr>
                                    <th class="py-2 pr-4">Curso</th>
                                    <th class="py-2 pr-4">Empresa</th>
                                    <th class="py-2 pr-4">Modalidad</th>
                                    <th class="py-2 pr-4">Inicio</th>
                                    <th class="py-2 pr-4">Estado</th>
                                    <th class="py-2 pr-4 text-right">Participantes</th>
                                    <th class="py-2 pr-4 text-right">Acciones</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y">
                                @forelse($executions as $execution)
                                    <tr class="hover:bg-gray-50">

                                        {{-- Curso + código --}}
                                        <td class="py-3 pr-4">
                                            <div class="font-medium text-gray-900">
                                                {{ $execution->course_name }}
                                            </div>
                                            <div class="text-gray-500">
                                                <span class="font-medium">
                                                    {{ $execution->internal_code }}
                                                </span>
                                            </div>
                                        </td>

                                        {{-- Empresa --}}
                                        <td class="py-3 pr-4 text-gray-700">
                                            {{ $execution->company->name ?? '—' }}
                                        </td>

                                        {{-- Modalidad --}}
                                        <td class="py-3 pr-4 text-gray-700">
                                            {{ $execution->modality }}
                                        </td>

                                        {{-- Inicio --}}
                                        <td class="py-3 pr-4 text-gray-700">
                                            {{ \Carbon\Carbon::parse($execution->start_date)->format('d-m-Y') }}
                                        </td>

                                        {{-- Estado --}}
                                        <td class="py-3 pr-4">
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                                {{ ucfirst(str_replace('_',' ',$execution->status)) }}
                                            </span>
                                        </td>
                                        {{-- Participantes --}}
                                        <td class="py-3 pr-4 text-right text-gray-900 font-medium">
                                            {{ $execution->participants->count() }}
                                        </td>
                                        
                                        {{-- Acciones --}}
                                        <td class="py-3 pr-4 text-right">
                                            <a href="{{ route('executions.show', $execution) }}"
                                               class="text-gray-700 hover:text-gray-900 font-medium">
                                                Ver
                                            </a>

                                            <a href="{{ route('executions.edit', $execution) }}"
                                               class="ml-3 text-indigo-600 hover:text-indigo-900 font-medium">
                                                Editar
                                            </a>

                                            <form action="{{ route('executions.destroy', $execution) }}"
                                                  method="POST"
                                                  class="inline"
                                                  onsubmit="return confirm('¿Eliminar esta ejecución?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="ml-3 text-red-600 hover:text-red-800 font-medium">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-8 text-center text-gray-500">
                                            No hay ejecuciones aún.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $executions->links() ?? '' }}
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>