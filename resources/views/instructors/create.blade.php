<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nuevo relator
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-breadcrumb :items="$breadcrumbs" />

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
           <form method="POST"
      action="{{ route('instructors.store') }}"
      enctype="multipart/form-data">

    @csrf

    @include('instructors.partials.form', ['instructor' => null])

    <x-file-upload
        label="Documentos del relator (PDF)"
    />

    <div class="mt-4 flex justify-end gap-2">
        <a href="{{ route('instructors.index') }}"
           class="px-4 py-2 rounded bg-gray-100">
            Volver
        </a>

        <button class="px-4 py-2 rounded bg-indigo-600 text-white">
            Guardar
        </button>
    </div>
</form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
