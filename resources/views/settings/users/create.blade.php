<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear usuario
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @isset($breadcrumbs)
                <x-breadcrumb :items="$breadcrumbs" />
            @endisset

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">

                    <form method="POST" action="{{ route('settings.users.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        @include('settings.users._form', ['modules' => $modules, 'positions' => $positions])

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <a href="{{ route('settings.users.index') }}" class="text-sm text-gray-600 hover:underline">
                                Cancelar
                            </a>

                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Guardar
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
