<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Capacitación Interna</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[
                ['label' => 'Ver Norma', 'url' => route('quality.norm.index')],
                ['label' => 'Plan de Formación', 'url' => route('quality.internal-trainings.index')],
                ['label' => 'Editar'],
            ]" />

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6">

                <form method="POST" action="{{ route('quality.internal-trainings.update', $training) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    @include('quality.internal-trainings._form')

                    <div class="flex items-center gap-3 pt-2">
                        <button class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                            Guardar
                        </button>
                        <a href="{{ route('quality.internal-trainings.index') }}" class="text-sm text-gray-600 hover:underline">Volver</a>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
