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

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    @if (session('status'))
                        <div class="mb-4 p-3 rounded bg-green-50 text-green-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left border-b">
                                    <th class="py-2">RUT</th>
                                    <th class="py-2">Nombre</th>
                                    <th class="py-2">Contacto</th>
                                    <th class="py-2">Email</th>
                                    <th class="py-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($companies as $company)
                                    <tr class="border-b">
                                        <td class="py-2">{{ $company->rut }}</td>
                                        <td class="py-2 font-medium">{{ $company->name }}</td>
                                        <td class="py-2">{{ $company->contact_name }}</td>
                                        <td class="py-2">{{ $company->email }}</td>
                                        <td class="py-2 text-right">
                                            <a class="underline text-sm"
                                               href="{{ route('companies.edit', $company) }}">
                                                Editar
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-gray-500">
                                            No hay empresas registradas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $companies->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
