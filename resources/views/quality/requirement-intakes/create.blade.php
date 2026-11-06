<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nuevo Levantamiento del Requerimiento</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[
                ['label' => 'Ver Norma', 'url' => route('quality.norm.index')],
                ['label' => 'Levantamiento del Requerimiento', 'url' => route('quality.requirement-intakes.index')],
                ['label' => 'Nuevo'],
            ]" />

            <form method="POST" action="{{ route('quality.requirement-intakes.store') }}" enctype="multipart/form-data">
                @csrf

                @include('quality.requirement-intakes._form')

                <div class="flex items-center gap-3 mt-6">
                    <button class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                        Registrar
                    </button>
                    <a href="{{ route('quality.requirement-intakes.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
