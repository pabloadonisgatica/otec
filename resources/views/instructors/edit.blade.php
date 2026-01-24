<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar relator
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-breadcrumb :items="$breadcrumbs" />

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 space-y-6">

                    <form method="POST" action="{{ route('instructors.update', $instructor) }}" class="space-y-4" enctype="multipart/form-data"
>
                        @csrf
                        @method('PUT')

                        @include('instructors.partials.form', ['instructor' => $instructor])
                       <x-file-upload
                            mode="upload"
                            label="Subir nuevos documentos (PDF)"
                        />


                        <div class="pt-4 flex justify-end gap-2">
                            <a href="{{ route('instructors.index') }}"
                               class="px-4 py-2 rounded bg-gray-100 text-gray-800">
                                Volver
                            </a>

                            <button type="submit"
                                    class="px-4 py-2 rounded bg-indigo-600 text-white">
                                Guardar cambios
                            </button>
                        </div>
                    </form>
                    <x-file-upload
                        mode="manage"
                        :existing="$instructor->documents ?? []"
                        type="instructors"
                        :ownerId="$instructor->id"
                        label="Documentos actuales"
                    />

                    <div class="pt-2 border-t">
                        <form method="POST"
                              action="{{ route('instructors.destroy', $instructor) }}"
                              onsubmit="return confirm('¿Seguro que deseas eliminar este relator?');">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="px-4 py-2 rounded bg-red-600 text-white">
                                Eliminar relator
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
