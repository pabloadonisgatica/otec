<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar empresa
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-breadcrumb :items="$breadcrumbs" />

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 space-y-6">

                    {{-- FORM UPDATE --}}
                    <form method="POST" action="{{ route('companies.update', $company) }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        @include('companies.partials.form', ['company' => $company])

                        <div class="pt-4 flex justify-end gap-2">
                            <a href="{{ route('companies.index') }}"
                               class="px-4 py-2 rounded bg-gray-100 text-gray-800">
                                Volver
                            </a>

                            <button type="submit"
                                    class="px-4 py-2 rounded bg-indigo-600 text-white">
                                Guardar cambios
                            </button>
                        </div>
                    </form>

                    {{-- FORM DELETE (SEPARADO, NO ANIDADO) --}}
                    <div class="pt-2 border-t">
                        <form method="POST"
                              action="{{ route('companies.destroy', $company) }}"
                              onsubmit="return confirm('¿Seguro que deseas eliminar esta empresa?');">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="px-4 py-2 rounded bg-red-600 text-white">
                                Eliminar empresa
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
