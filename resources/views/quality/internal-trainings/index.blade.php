<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Registro de Capacitación Interna</h2>

            <a href="{{ route('quality.internal-trainings.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                Crear
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[
                ['label' => 'Ver Norma', 'url' => route('quality.norm.index')],
                ['label' => 'Plan de Formación'],
            ]" />

            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-green-50 text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b bg-gray-50">
                                <th class="py-2 px-3">Nombre Actividad</th>
                                <th class="py-2 px-3">N° Horas</th>
                                <th class="py-2 px-3">Fecha</th>
                                <th class="py-2 px-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($trainings as $training)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-3">{{ $training->activity_name }}</td>
                                    <td class="py-2 px-3 text-gray-600">{{ $training->hours ?? '—' }}</td>
                                    <td class="py-2 px-3 text-gray-600 whitespace-nowrap">{{ $training->activity_date->format('d-m-Y') }}</td>
                                    <td class="py-2 px-3 text-right whitespace-nowrap">
                                        <a href="{{ route('quality.internal-trainings.pdf', $training) }}" target="_blank" class="text-indigo-600 hover:underline text-xs font-medium">Ver PDF</a>
                                        <span class="text-gray-300 mx-1">|</span>
                                        <a href="{{ route('quality.internal-trainings.edit', $training) }}" class="text-gray-600 hover:underline text-xs font-medium">Modificar</a>
                                        <span class="text-gray-300 mx-1">|</span>
                                        <form method="POST" action="{{ route('quality.internal-trainings.destroy', $training) }}"
                                              class="inline" onsubmit="return confirm('¿Eliminar este registro?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:underline text-xs font-medium">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-gray-500">
                                        No hay capacitaciones internas registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $trainings->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
