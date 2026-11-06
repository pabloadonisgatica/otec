<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Levantamiento del Requerimiento</h2>

            <a href="{{ route('quality.requirement-intakes.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                Nuevo levantamiento
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[
                ['label' => 'Ver Norma', 'url' => route('quality.norm.index')],
                ['label' => 'Levantamiento del Requerimiento'],
            ]" />

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">

                    <form method="GET" action="{{ route('quality.requirement-intakes.index') }}"
                          class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 pb-6 border-b border-gray-200">
                        <input type="hidden" name="status" value="{{ $status }}">

                        <div class="md:col-span-2">
                            <label class="block text-xs text-gray-500">Buscar</label>
                            <input type="text" name="q" value="{{ $search }}" placeholder="Código, título o contacto"
                                   class="mt-1 w-full text-sm rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500">Empresa</label>
                            <select name="company_id" class="mt-1 w-full text-sm rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todas</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" @selected($companyId == $company->id)>{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-end gap-2">
                            <button class="px-4 py-2 rounded-md bg-gray-800 text-white text-xs font-semibold uppercase tracking-widest hover:bg-gray-700">
                                Buscar
                            </button>
                            <a href="{{ route('quality.requirement-intakes.index') }}" class="px-4 py-2 rounded-md border border-gray-300 text-gray-600 text-xs hover:bg-gray-50">
                                Limpiar
                            </a>
                        </div>
                    </form>

                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach(['' => 'Todos'] + \App\Models\ClientRequirementIntake::REVIEW_STATUSES as $value => $label)
                            <a href="{{ route('quality.requirement-intakes.index', array_filter(['status' => $value, 'q' => $search, 'company_id' => $companyId])) }}"
                               class="px-3 py-1.5 rounded-full text-xs font-medium {{ $status === (string) $value ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left border-b bg-gray-50">
                                    <th class="py-2 px-3">Código</th>
                                    <th class="py-2 px-3">Solicitud</th>
                                    <th class="py-2 px-3">Empresa</th>
                                    <th class="py-2 px-3">Título</th>
                                    <th class="py-2 px-3">Revisión</th>
                                    <th class="py-2 px-3">Presupuesto</th>
                                    <th class="py-2 px-3"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($intakes as $intake)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-2 px-3">
                                            <a href="{{ route('quality.requirement-intakes.show', $intake) }}" class="font-mono text-indigo-600 hover:underline">{{ $intake->code }}</a>
                                        </td>
                                        <td class="py-2 px-3 text-gray-600 whitespace-nowrap">{{ $intake->requested_at->format('d-m-Y') }}</td>
                                        <td class="py-2 px-3">{{ $intake->company?->name ?? 'Empresa eliminada' }}</td>
                                        <td class="py-2 px-3">{{ $intake->title }}</td>
                                        <td class="py-2 px-3 whitespace-nowrap">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $intake->reviewStatusBadge() }}">{{ $intake->reviewStatusLabel() }}</span>
                                            @if($intake->reviewer)
                                                <div class="text-xs text-gray-500 mt-1">{{ $intake->reviewer->name }} · {{ optional($intake->reviewed_at)->format('d-m-Y') }}</div>
                                            @endif
                                        </td>
                                        <td class="py-2 px-3 text-gray-600">{{ $intake->budget?->internal_name ?? '—' }}</td>
                                        <td class="py-2 px-3 text-right whitespace-nowrap">
                                            <a href="{{ route('quality.requirement-intakes.show', $intake) }}" class="text-indigo-600 hover:underline text-xs font-medium">Ver</a>
                                            <span class="text-gray-300 mx-1">|</span>
                                            <a href="{{ route('quality.requirement-intakes.edit', $intake) }}" class="text-gray-600 hover:underline text-xs font-medium">Editar</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-6 text-center text-gray-500">No hay levantamientos registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">{{ $intakes->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
