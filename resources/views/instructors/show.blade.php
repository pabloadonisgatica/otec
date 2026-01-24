<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $instructor->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-breadcrumb :items="$breadcrumbs" />

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 space-y-6">

                    <div class="space-y-1">
                        <div><strong>RUT:</strong> {{ $instructor->rut }}</div>
                        <div><strong>Email:</strong> {{ $instructor->email ?? '-' }}</div>
                        <div><strong>Teléfono:</strong> {{ $instructor->phone ?? '-' }}</div>
                        <div><strong>Profesión:</strong> {{ $instructor->profession ?? '-' }}</div>
                        <div><strong>Bio:</strong> {{ $instructor->bio ?? '-' }}</div>
                    </div>

                    <x-file-upload
                        mode="manage"
                        :existing="$instructor->documents ?? []"
                        type="instructors"
                        :ownerId="$instructor->id"
                        label="Documentos"
                    />

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('instructors.edit', $instructor) }}"
                           class="px-4 py-2 rounded bg-indigo-600 text-white">
                            Editar
                        </a>
                        <a href="{{ route('instructors.index') }}"
                           class="px-4 py-2 rounded bg-gray-100">
                            Volver
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
