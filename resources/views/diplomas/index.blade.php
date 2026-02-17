<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Diplomas emitidos</h2>

            <a href="{{ route('diplomas.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                Emitir diplomas
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-green-50 text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b">
                                <th class="py-2 pr-4">Código</th>
                                <th class="py-2 pr-4">Participante</th>
                                <th class="py-2 pr-4">Curso</th>
                                <th class="py-2 pr-4">Plantilla</th>
                                <th class="py-2 pr-4">Emitido</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($diplomas as $d)
                                <tr class="border-b">
                                    <td class="py-2 pr-4 font-mono">{{ $d->code }}</td>
                                    <td class="py-2 pr-4">{{ $d->participant->full_name ?? '-' }}</td>
                                    <td class="py-2 pr-4">{{ $d->course->name ?? '-' }}</td>
                                    <td class="py-2 pr-4">{{ $d->template->name ?? '-' }}</td>
                                    <td class="py-2 pr-4 text-gray-600">{{ optional($d->issued_at)->format('Y-m-d') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-gray-500">
                                        No hay diplomas emitidos aún.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $diplomas->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
