<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $profile->position_name }}</h2>

            <a href="{{ route('quality.job-profiles.edit', $profile) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-gray-50">
                Editar
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[
                ['label' => 'Ver Norma', 'url' => route('quality.norm.index')],
                ['label' => 'Perfil de Cargo', 'url' => route('quality.job-profiles.index')],
                ['label' => $profile->position_name],
            ]" />

            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-green-50 text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6 space-y-4">

                <div>
                    <p class="text-sm text-gray-500">Fecha</p>
                    <p class="text-gray-900">{{ optional($profile->profile_date)->format('d-m-Y') ?? '—' }}</p>
                </div>

                @if($profile->functions)
                    <div>
                        <p class="text-sm text-gray-500">Funciones y Tareas del Puesto</p>
                        <p class="text-gray-900 whitespace-pre-line">{{ $profile->functions }}</p>
                    </div>
                @endif

                @if($profile->responsibilities)
                    <div>
                        <p class="text-sm text-gray-500">Responsabilidades</p>
                        <p class="text-gray-900 whitespace-pre-line">{{ $profile->responsibilities }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Área</p>
                        <p class="text-gray-900">{{ $profile->area ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Dependencia Directa De</p>
                        <p class="text-gray-900">{{ $profile->reports_to ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Personas a su Cargo</p>
                        <p class="text-gray-900">{{ $profile->direct_reports ?? '—' }}</p>
                    </div>
                </div>

                @foreach([
                    'education_requirements' => 'Requisitos relativos a la Educación',
                    'training_requirements' => 'Requisitos relativos a la Formación',
                    'skills_requirements' => 'Requisitos relativos a las Habilidades',
                    'experience_requirements' => 'Requisitos relativos a la Experiencia',
                ] as $field => $label)
                    @if($profile->$field)
                        <div>
                            <p class="text-sm text-gray-500">{{ $label }}</p>
                            <p class="text-gray-900 whitespace-pre-line">{{ $profile->$field }}</p>
                        </div>
                    @endif
                @endforeach

                <div class="border-t border-gray-100 pt-4">
                    <p class="text-sm text-gray-500 mb-2">Procedimientos asignados</p>
                    @if($profile->procedures->isEmpty())
                        <p class="text-sm text-gray-400">Ninguno asignado.</p>
                    @else
                        <ul class="list-disc list-inside text-sm text-gray-900">
                            @foreach($profile->procedures as $procedure)
                                <li>
                                    <a href="{{ route('quality.procedures.show', $procedure) }}" class="text-indigo-600 hover:underline">
                                        {{ $procedure->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
