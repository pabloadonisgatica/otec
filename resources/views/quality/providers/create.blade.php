<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nuevo Proveedor</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @include('quality._nav')

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <form method="POST" action="{{ route('quality.providers.store') }}" class="space-y-4">
                        @csrf

                        @include('quality.providers._form', ['provider' => null])

                        <div class="pt-4 flex justify-end gap-2">
                            <a href="{{ route('quality.providers.index') }}"
                               class="px-4 py-2 rounded bg-gray-100 text-gray-800">
                                Volver
                            </a>

                            <button type="submit"
                                    class="px-4 py-2 rounded bg-indigo-600 text-white">
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
