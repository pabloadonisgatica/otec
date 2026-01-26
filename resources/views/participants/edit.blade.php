<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar Participante
            </h2>

            <a href="{{ route('participants.index') }}"
               class="underline text-sm text-gray-700 hover:text-gray-900">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="$breadcrumbs" />

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">

                    <form method="POST" action="{{ route('participants.update', $participant) }}">
                        @csrf
                        @method('PUT')

                        @include('participants.partials.form')

                        <div class="mt-6 flex justify-end gap-2">
                            <a href="{{ route('participants.index') }}"
                               class="px-4 py-2 rounded bg-gray-100 text-gray-800 hover:bg-gray-200">
                                Cancelar
                            </a>

                            <button type="submit"
                                    class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-500">
                                Guardar cambios
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
