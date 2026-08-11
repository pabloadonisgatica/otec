<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Diplomas emitidos</h2>

            <div class="flex gap-2">
                <a href="{{ route('diploma-templates.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-gray-50">
                    Plantillas
                </a>

                <a href="{{ route('diplomas.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                    Emitir diplomas
                </a>
            </div>
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
                <div class="p-6">

                    <form method="GET" action="{{ route('diplomas.index') }}" class="mb-4">
                        <input
                            type="text"
                            name="q"
                            value="{{ $search }}"
                            placeholder="Buscar por participante, RUT, código o ejecución…"
                            class="w-full max-w-md rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </form>

                    <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b">
                                <th class="py-2 pr-4">Código</th>
                                <th class="py-2 pr-4">Participante</th>
                                <th class="py-2 pr-4">Ejecución</th>
                                <th class="py-2 pr-4">Plantilla</th>
                                <th class="py-2 pr-4">Emitido</th>
                                <th class="py-2 pr-4"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($diplomas as $d)
                                <tr class="border-b">
                                    <td class="py-2 pr-4 font-mono">{{ $d->code }}</td>
                                    <td class="py-2 pr-4">
                                        {{ $d->participant->first_name ?? '' }} {{ $d->participant->last_name ?? '' }}
                                        <span class="text-gray-500 text-xs">{{ $d->participant->rut ?? '' }}</span>
                                    </td>
                                    <td class="py-2 pr-4">
                                        {{ $d->execution->internal_code ?? '-' }}
                                        <span class="text-gray-500">{{ $d->execution->course_name ?? '' }}</span>
                                    </td>
                                    <td class="py-2 pr-4">{{ $d->template->name ?? '-' }}</td>
                                    <td class="py-2 pr-4 text-gray-600">{{ optional($d->issued_at)->format('Y-m-d') }}</td>
                                    <td class="py-2 pr-4 text-right whitespace-nowrap">
                                        <a href="{{ route('diplomas.pdf', $d) }}" target="_blank" class="text-indigo-600 hover:underline text-xs font-medium">
                                            PDF
                                        </a>
                                        <span class="text-gray-300 mx-1">|</span>
                                        <form method="POST" action="{{ route('diplomas.destroy', $d) }}" class="inline"
                                              onsubmit="return confirm('¿Eliminar este diploma? Podrás volver a emitirlo después.')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:underline text-xs font-medium">
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-gray-500">
                                        No hay diplomas emitidos aún.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>

                    <div class="mt-4">
                        {{ $diplomas->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
