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
                                        <x-ui.row-actions>
                                            <x-ui.row-actions.link :href="route('quality.internal-trainings.pdf', $training)" target="_blank">Ver PDF</x-ui.row-actions.link>

                                            <x-slot:menu>
                                                <x-ui.row-actions.item :href="route('quality.internal-trainings.edit', $training)">Editar</x-ui.row-actions.item>
                                                <x-ui.row-actions.divider />
                                                <x-ui.row-actions.item :action="route('quality.internal-trainings.destroy', $training)" method="DELETE" danger confirm="¿Eliminar este registro?">Eliminar</x-ui.row-actions.item>
                                            </x-slot:menu>
                                        </x-ui.row-actions>
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
