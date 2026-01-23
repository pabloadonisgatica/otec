<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Empresas
            </h2>

            <a href="{{ route('companies.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Nueva empresa
            </a>
        </div>
    </x-slot>

    <div class="py-6" x-data="detailModal('company-detail')" x-cloak>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <x-breadcrumb :items="$breadcrumbs" />

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">

                    {{-- Mensaje --}}
                    @if (session('status'))
                        <div class="mb-4 p-3 rounded bg-green-50 text-green-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{-- Buscador --}}
                    <x-table.search
                        :value="$q"
                        placeholder="Buscar por RUT, nombre, fantasía, email o contacto…"
                    />

                    {{-- Tabla --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left border-b">
                                    <th class="py-2">RUT</th>
                                    <th class="py-2">Nombre</th>
                                    <th class="py-2">Contacto</th>
                                    <th class="py-2">Email</th>
                                    <th class="py-2 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($companies as $company)
                                    <tr class="border-b">
                                        <td class="py-2">{{ $company->rut }}</td>
                                        <td class="py-2 font-medium">{{ $company->name }}</td>
                                        <td class="py-2">{{ $company->contact_name }}</td>
                                        <td class="py-2">{{ $company->email }}</td>

                                        <td class="py-2 text-right whitespace-nowrap">
                                            {{-- Ver detalle --}}
                                            <button type="button"
                                                    class="text-indigo-600 hover:underline"
                                                    @click="open('{{ route('companies.show', $company, false) }}')">
                                                Ver detalle
                                            </button>

                                            <span class="mx-2 text-gray-300">|</span>

                                            {{-- Editar --}}
                                            <a class="underline text-sm"
                                               href="{{ route('companies.edit', $company) }}">
                                                Editar
                                            </a>

                                            <span class="mx-2 text-gray-300">|</span>

                                            {{-- Eliminar --}}
                                            <form method="POST"
                                                  action="{{ route('companies.destroy', $company) }}"
                                                  class="inline"
                                                  onsubmit="return confirm('¿Seguro que deseas eliminar esta empresa?');">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="underline text-sm text-red-600 hover:text-red-800">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"
                                            class="py-6 text-center text-gray-500">
                                            No hay empresas registradas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación --}}
                    <div class="mt-4">
                        {{ $companies->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL: Detalle Empresa --}}
        <x-modal name="company-detail" maxWidth="2xl">
            <div class="p-6">
                <div class="flex items-start justify-between gap-4">
                    <h2 class="text-lg font-semibold">
                        Detalle Empresa
                    </h2>

                    <button type="button"
                            class="text-gray-500 hover:text-gray-800"
                            @click="close()">
                        ✕
                    </button>
                </div>

                <template x-if="loading">
                    <div class="mt-4 text-sm text-gray-600">
                        Cargando…
                    </div>
                </template>

                <template x-if="error">
                    <div class="mt-4 text-sm text-red-600"
                         x-text="error">
                    </div>
                </template>

                <template x-if="data && !loading">
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        <div><strong>RUT:</strong> <span x-text="data.rut"></span></div>
                        <div><strong>Nombre:</strong> <span x-text="data.name"></span></div>

                        <div><strong>Fantasía:</strong> <span x-text="data.business_name ?? '-'"></span></div>
                        <div><strong>Email:</strong> <span x-text="data.email ?? '-'"></span></div>

                        <div><strong>Teléfono:</strong> <span x-text="data.phone ?? '-'"></span></div>
                        <div><strong>Dirección:</strong> <span x-text="data.address ?? '-'"></span></div>

                        <div><strong>Región:</strong> <span x-text="data.region ?? '-'"></span></div>
                        <div><strong>Comuna:</strong> <span x-text="data.commune ?? '-'"></span></div>

                        <div><strong>Contacto:</strong> <span x-text="data.contact_name ?? '-'"></span></div>
                        <div><strong>Email contacto:</strong> <span x-text="data.contact_email ?? '-'"></span></div>
                        <div><strong>Tel. contacto:</strong> <span x-text="data.contact_phone ?? '-'"></span></div>
                    </div>
                </template>

                <div class="mt-6 flex justify-end">
                    <button type="button"
                            class="px-4 py-2 rounded bg-gray-800 text-white"
                            @click="close()">
                        Cerrar
                    </button>
                </div>
            </div>
        </x-modal>
    </div>
</x-app-layout>
