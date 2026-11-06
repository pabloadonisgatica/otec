<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Carga masiva de participantes
            </h2>

            <a href="{{ route('participants.index') }}"
               class="underline text-sm text-gray-700 hover:text-gray-900">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="$breadcrumbs" />

            @include('participants.import._steps', ['current' => 1])

            <x-ui.card>
                <div class="p-6 space-y-6">

                    <div class="text-sm text-gray-700 space-y-2">
                        <p>
                            Sube el Excel o CSV tal como lo tengas, por ejemplo el que envía el cliente.
                            <strong>No necesita un formato especial:</strong> en el siguiente paso indicas qué columna es cada dato.
                        </p>
                        <p class="text-gray-500">
                            Se necesitan al menos RUT, nombres y apellidos. Correo y teléfono son opcionales.
                            Máximo {{ $maxRows }} filas por carga.
                            <a href="{{ route('participants.import.template') }}" class="text-indigo-700 underline hover:text-indigo-900">Descargar plantilla Excel (opcional)</a>
                        </p>
                    </div>

                    <form method="POST" action="{{ route('participants.import') }}" enctype="multipart/form-data" class="space-y-4"
                          x-data="{ busy: false }" @submit="busy = true">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Empresa *</label>
                            <select name="company_id" class="mt-1 w-full rounded-md border-gray-300" required>
                                <option value="">Selecciona una empresa…</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" @selected(old('company_id', request('company_id')) == $company->id)>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Todos los participantes del archivo quedan asociados a esta empresa.</p>
                            @error('company_id') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Archivo Excel o CSV *</label>
                            <input type="file" name="file" accept=".xlsx,.xls,.csv" class="mt-1 block w-full text-sm" required>
                            @error('file') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="flex justify-end gap-2 pt-4">
                            <a href="{{ route('participants.index') }}"
                               class="px-4 py-2 rounded bg-gray-100 text-gray-800 hover:bg-gray-200">
                                Cancelar
                            </a>

                            <button type="submit" :disabled="busy"
                                    class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-500 disabled:opacity-50"
                                    x-text="busy ? 'Leyendo archivo…' : 'Continuar'">
                                Continuar
                            </button>
                        </div>
                    </form>

                </div>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
