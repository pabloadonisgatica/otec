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

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 space-y-6">

                    <div class="text-sm text-gray-700">
                        <p class="font-semibold mb-2">Formato CSV</p>
                        <pre class="p-3 bg-gray-100 rounded text-xs">rut,first_name,last_name,email,phone,status</pre>
                        <p class="mt-2 text-gray-600">
                            La empresa se selecciona aquí. El archivo no incluye empresa.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('participants.import') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Empresa *</label>
                            <select name="company_id" class="mt-1 w-full rounded-md border-gray-300" required>
                                <option value="">Selecciona una empresa…</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" @selected(old('company_id') == $company->id)>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('company_id') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Archivo CSV *</label>
                            <input type="file" name="file" accept=".csv" class="mt-1 block w-full text-sm" required>
                            @error('file') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="flex justify-end gap-2 pt-4">
                            <a href="{{ route('participants.index') }}"
                               class="px-4 py-2 rounded bg-gray-100 text-gray-800 hover:bg-gray-200">
                                Cancelar
                            </a>

                            <button type="submit"
                                    class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-500">
                                Importar
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
