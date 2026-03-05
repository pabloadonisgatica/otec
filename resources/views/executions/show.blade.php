<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Ejecución {{ $execution->internal_code }}
            </h2>

            <a href="{{ route('executions.edit', $execution) }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                Editar
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="$breadcrumbs" />
 {{-- Resumen adicional --}}
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Participantes --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-gray-500 text-sm">Participantes</div>
                    <div class="text-2xl font-bold text-gray-900">
                        {{ $execution->participants->count() }}
                    </div>
                </div>

                {{-- Relatores --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-gray-500 text-sm">Relatores</div>
                    <div class="text-2xl font-bold text-gray-900">
                        {{ $execution->instructors->count() }}
                    </div>
                </div>

                {{-- Horas programadas --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-gray-500 text-sm">Horas programadas</div>
                    <div class="text-2xl font-bold text-gray-900">
                        {{ $execution->sessions->sum('hours') }}
                    </div>
                </div>

            </div>
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">

                    {{-- Curso --}}
                    <div>
                        <span class="text-gray-500">Curso</span>
                        <div class="font-medium text-gray-900">
                            {{ $execution->course_name }}
                        </div>
                    </div>

                    {{-- Empresa --}}
                    <div>
                        <span class="text-gray-500">Empresa</span>
                        <div class="font-medium text-gray-900">
                            {{ $execution->company->name ?? '—' }}
                        </div>
                    </div>

                    {{-- Modalidad --}}
                    <div>
                        <span class="text-gray-500">Modalidad</span>
                        <div class="font-medium text-gray-900">
                            {{ $execution->modality }}
                        </div>
                    </div>

                    {{-- Fecha inicio --}}
                    <div>
                        <span class="text-gray-500">Fecha inicio</span>
                        <div class="font-medium text-gray-900">
                            {{ \Carbon\Carbon::parse($execution->start_date)->format('d-m-Y') }}
                        </div>
                    </div>

                    {{-- Estado --}}
                    <div>
                        <span class="text-gray-500">Estado</span>
                        <div class="font-medium text-gray-900">
                            {{ ucfirst(str_replace('_',' ',$execution->status)) }}
                        </div>
                    </div>

                    {{-- Tipo evaluación --}}
                    <div>
                        <span class="text-gray-500">Tipo evaluación</span>
                        <div class="font-medium text-gray-900">
                            {{ $execution->evaluation_type === 'percentage' ? 'Porcentaje' : 'Nota' }}
                        </div>
                    </div>
                    {{-- Tipo --}}
                    <div>
                        <span class="text-gray-500">Tipo</span>
                        <div class="font-medium text-gray-900">
                            {{ ucfirst($execution->type) }}
                        </div>
                    </div>
                </div>
            </div>

           

        </div>
    </div>
</x-app-layout>