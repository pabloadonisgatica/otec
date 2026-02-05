<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar usuario
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @isset($breadcrumbs)
                <x-breadcrumb :items="$breadcrumbs" />
            @endisset

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">

                    @if (session('status'))
                        <div class="mb-4 p-3 rounded bg-green-50 text-green-700 text-sm">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('settings.users.update', $user) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        @include('settings.users._form', ['modules' => $modules, 'user' => $user])

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <a href="{{ route('settings.users.index') }}" class="text-sm text-gray-600 hover:underline">
                                Volver
                            </a>

                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Guardar cambios
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
