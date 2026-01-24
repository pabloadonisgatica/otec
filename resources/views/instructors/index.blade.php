<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Relatores
            </h2>

            <a href="{{ route('instructors.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Nuevo relator
            </a>
        </div>
    </x-slot>

    <div class="py-6" x-data="detailModal('instructor-detail')" x-cloak>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="$breadcrumbs" />

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">

                    @if (session('status'))
                        <div class="mb-4 p-3 rounded bg-green-50 text-green-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    <x-table.search :value="$q" placeholder="Buscar por RUT, nombre, email, teléfono o profesión…" />

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left border-b">
                                    <th class="py-2">RUT</th>
                                    <th class="py-2">Nombre</th>
                                    <th class="py-2">Profesión</th>
                                    <th class="py-2">Email</th>
                                    <th class="py-2 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($instructors as $instructor)
                                    <tr class="border-b">
                                        <td class="py-2">{{ $instructor->rut }}</td>
                                        <td class="py-2 font-medium">{{ $instructor->name }}</td>
                                        <td class="py-2">{{ $instructor->profession }}</td>
                                        <td class="py-2">{{ $instructor->email }}</td>
                                        <td class="py-2 text-right whitespace-nowrap">
                                            <button type="button"
                                                    class="text-indigo-600 hover:underline"
                                                    @click="open('{{ route('instructors.json', $instructor, false) }}')">
                                                Ver detalle
                                            </button>

                                            <span class="mx-2 text-gray-300">|</span>

                                            <a class="underline text-sm"
                                               href="{{ route('instructors.edit', $instructor) }}">
                                                Editar
                                            </a>

                                            <span class="mx-2 text-gray-300">|</span>

                                            <form method="POST"
                                                  action="{{ route('instructors.destroy', $instructor) }}"
                                                  class="inline"
                                                  onsubmit="return confirm('¿Seguro que deseas eliminar este relator?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="underline text-sm text-red-600 hover:text-red-800">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-gray-500">
                                            No hay relatores registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $instructors->links() }}
                    </div>
                </div>
            </div>
        </div>

        <x-modal name="instructor-detail" maxWidth="2xl">
            <div class="p-6">
                <div class="flex items-start justify-between gap-4">
                    <h2 class="text-lg font-semibold">Detalle Relator</h2>
                    <button type="button" class="text-gray-500 hover:text-gray-800" @click="close()">✕</button>
                </div>

                <template x-if="loading">
                    <div class="mt-4 text-sm text-gray-600">Cargando…</div>
                </template>

                <template x-if="error">
                    <div class="mt-4 text-sm text-red-600" x-text="error"></div>
                </template>

                <template x-if="data && !loading">
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        <div><strong>RUT:</strong> <span x-text="data.rut"></span></div>
                        <div><strong>Nombre:</strong> <span x-text="data.name"></span></div>
                        <div><strong>Email:</strong> <span x-text="data.email ?? '-'"></span></div>
                        <div><strong>Teléfono:</strong> <span x-text="data.phone ?? '-'"></span></div>
                        <div><strong>Profesión:</strong> <span x-text="data.profession ?? '-'"></span></div>
                        <div class="md:col-span-2"><strong>Bio:</strong> <span x-text="data.bio ?? '-'"></span></div>
<div class="md:col-span-2 mt-4">
    <h3 class="font-semibold">Documentos</h3>

    <template x-if="!data.documents || data.documents.length === 0">
        <div class="mt-2 text-sm text-gray-500">Sin documentos</div>
    </template>

    <div class="mt-2 space-y-2 text-sm">
        <template x-for="(doc, i) in (data.documents ?? [])" :key="i">
            <template x-if="doc && doc.path">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <strong x-text="doc.label ?? 'Documento'"></strong>:
                        <span x-text="doc.name ?? doc.path.split('/').pop()"></span>
                    </div>

                    <div class="flex gap-2">
                        <a class="text-blue-600 underline"
                           :href="`/documents/instructors/${data.id}/${i}`"
                           target="_blank">
                            Ver
                        </a>

                        <a class="text-blue-600 underline"
                           :href="`/documents/instructors/${data.id}/${i}/download`">
                            Descargar
                        </a>
                    </div>
                </div>
            </template>
        </template>
    </div>
</div>

                    </div>
                </template>

                <div class="mt-6 flex justify-end">
                    <button type="button" class="px-4 py-2 rounded bg-gray-800 text-white" @click="close()">Cerrar</button>
                </div>
            </div>
        </x-modal>
    </div>
</x-app-layout>
