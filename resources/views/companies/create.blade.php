<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nueva Empresa
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <form method="POST" action="{{ route('companies.store') }}" class="space-y-4">
                        @csrf

                        @include('companies.partials.form', ['company' => null])

                        <div class="flex gap-2">
                            <button class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm">
                                Guardar
                            </button>
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
