<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar ejecución {{ $execution->internal_code }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="$breadcrumbs" />

            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-green-50 text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">

                    <form method="POST" action="{{ route('executions.update', $execution) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Tipo --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tipo
                                </label>
                                <select name="type" class="w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="cerrado"
                                        {{ $execution->type === 'cerrado' ? 'selected' : '' }}>
                                        Cerrado
                                    </option>
                                    <option value="abierto"
                                        {{ $execution->type === 'abierto' ? 'selected' : '' }}>
                                        Abierto
                                    </option>
                                </select>
                            </div>
                            {{-- Curso --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Curso
                                </label>
                                <select name="course_id" class="w-full border-gray-300 rounded-md shadow-sm">
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}"
                                            {{ $execution->course_id == $course->id ? 'selected' : '' }}>
                                            {{ $course->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Empresa --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Empresa
                                </label>
                                <select name="company_id" class="w-full border-gray-300 rounded-md shadow-sm">
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}"
                                            {{ $execution->company_id == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
{{-- Modalidad (readonly) --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Modalidad
    </label>

    <input type="text"
           id="modality_display"
           value="{{ $execution->modality }}"
           class="w-full border-gray-300 rounded-md shadow-sm bg-gray-100"
           readonly>
</div>

{{-- Horas totales (readonly) --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Horas totales
    </label>

    <input type="text"
           id="hours_display"
           value="{{ $execution->course->hours ?? '' }}"
           class="w-full border-gray-300 rounded-md shadow-sm bg-gray-100"
           readonly>
</div>
                            {{-- Tipo evaluación --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tipo evaluación
                                </label>
                                <select name="evaluation_type" class="w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="percentage"
                                        {{ $execution->evaluation_type === 'percentage' ? 'selected' : '' }}>
                                        Porcentaje
                                    </option>
                                    <option value="grade"
                                        {{ $execution->evaluation_type === 'grade' ? 'selected' : '' }}>
                                        Nota
                                    </option>
                                </select>
                            </div>

                            {{-- Estado --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Estado
                                </label>
                                <select name="status" class="w-full border-gray-300 rounded-md shadow-sm">
                                    @foreach(['planificada','en_ejecucion','finalizada','cancelada'] as $status)
                                        <option value="{{ $status }}"
                                            {{ $execution->status === $status ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_',' ',$status)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Fecha inicio --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Fecha inicio
                                </label>
                                <input type="date"
                                       name="start_date"
                                       value="{{ $execution->start_date }}"
                                       class="w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                        </div>

                        {{-- Observaciones --}}
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Observaciones
                            </label>
                            <textarea name="observations"
                                      rows="3"
                                      class="w-full border-gray-300 rounded-md shadow-sm">{{ $execution->observations }}</textarea>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <a href="{{ route('executions.show', $execution) }}"
                               class="mr-3 px-4 py-2 text-sm text-gray-600 hover:text-gray-800">
                                Cancelar
                            </a>

                            <button type="submit"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-semibold hover:bg-indigo-500">
                                Actualizar
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>