<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Empresa
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <form method="POST" action="{{ route('companies.update', $company) }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        @include('companies.partials.form', ['company' => $company])

                        <div class="flex gap-2">
                            <button class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm">
                                Actualizar
                            </button>

                            <form method="POST" action="{{ route('companies.destroy', $company) }}"
                                  onsubmit="return confirm('¿Eliminar empresa?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 border rounded-lg text-sm">
                                    Eliminar
                                </button>
                            </form>

                            <a href="{{ route('companies.index') }}" class="px-4 py-2 border rounded-lg text-sm">
                                Volver
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
