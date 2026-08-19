<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Comunicación</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[
                ['label' => 'Ver Norma', 'url' => route('quality.norm.index')],
                ['label' => 'Comunicación Interna', 'url' => route('quality.internal-communications.index')],
                ['label' => 'Editar'],
            ]" />

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6">

                <form method="POST" action="{{ route('quality.internal-communications.update', $communication) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    @include('quality.internal-communications._form')

                    <div class="flex items-center gap-3 pt-2">
                        <button class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                            Guardar
                        </button>
                        <a href="{{ route('quality.internal-communications.index') }}" class="text-sm text-gray-600 hover:underline">Volver</a>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
