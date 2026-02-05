<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Presupuestos
            </h2>

            <a href="{{ route('budgets.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Nuevo presupuesto
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Breadcrumb (si estás usando breadcrumbs en este módulo) --}}
            @isset($breadcrumbs)
                <x-breadcrumb :items="$breadcrumbs" />
            @endisset

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">

                    @if (session('success'))
                        <div class="mb-4 p-3 rounded bg-green-50 text-green-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Search (si aún no implementas búsqueda, igual lo dejamos preparado) --}}
                    <x-table.search :value="$q ?? ''" placeholder="Buscar por nombre interno, empresa o curso…" />

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left border-b">
                                    <th class="py-2">ID</th>
                                    <th class="py-2">Nombre interno</th>
                                    <th class="py-2">Curso</th>
                                    <th class="py-2">Empresa</th>
                                    <th class="py-2 text-right">Valor</th>
                                    <th class="py-2">Estado</th>
                                    <th class="py-2 text-right">Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($budgets as $budget)
                                    @php
                                        $coursesCount = $budget->courses->count();
                                        $courseLabel = '-';

                                        if ($coursesCount === 1 && $budget->courses->first()?->course) {
                                            $courseLabel = $budget->courses->first()->course->name;
                                        } elseif ($coursesCount > 1) {
                                            $courseLabel = $coursesCount . ' cursos';
                                        }
                                    @endphp

                                    <tr class="border-b">
                                        <td class="py-2">{{ $budget->id }}</td>

                                        <td class="py-2 font-medium">
                                            {{ $budget->internal_name }}
                                        </td>

                                        <td class="py-2">
                                            {{ $courseLabel }}
                                        </td>

                                        <td class="py-2">
                                            {{ $budget->company->name ?? '-' }}
                                        </td>

                                        <td class="py-2 text-right font-medium">
                                            ${{ number_format($budget->total_amount ?? 0, 0, ',', '.') }}
                                        </td>

                                        <td class="py-2">
                                            {{ ucfirst($budget->status) }}
                                        </td>

                                        <td class="py-2 text-right whitespace-nowrap">
                                            <a class="underline text-sm"
                                               href="{{ route('budgets.edit', $budget) }}">
                                                Modificar
                                            </a>

                                            <span class="mx-2 text-gray-300">|</span>

                                            <a class="underline text-sm"
                                               href="{{ route('budgets.pdf', $budget) }}">
                                                PDF
                                            </a>

                                            <span class="mx-2 text-gray-300">|</span>

                                            <form method="POST"
                                                  action="{{ route('budgets.duplicate', $budget) }}"
                                                  class="inline"
                                                  onsubmit="return confirm('¿Duplicar este presupuesto?');">
                                                @csrf
                                                <button type="submit" class="underline text-sm text-gray-700 hover:text-gray-900">
                                                    Duplicar
                                                </button>
                                            </form>
                                            <span class="mx-2 text-gray-300">|</span>

                                            <form method="POST"
                                                action="{{ route('budgets.destroy', $budget) }}"
                                                class="inline"
                                                onsubmit="return confirm('¿Seguro que deseas eliminar este presupuesto?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="underline text-sm text-red-600 hover:text-red-800">
                                                    Eliminar
                                                </button>
                                            </form>




                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-6 text-center text-gray-500">
                                            No hay presupuestos registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $budgets->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
