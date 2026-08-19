<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Comunicación Interna</h2>

            <a href="{{ route('quality.internal-communications.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                Registrar Comunicación
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[
                ['label' => 'Ver Norma', 'url' => route('quality.norm.index')],
                ['label' => 'Comunicación Interna'],
            ]" />

            <p class="text-sm text-gray-500 mb-4">
                La norma exige que la Dirección comunique la Política de Calidad, los requisitos de NCh 2728:2015,
                los Objetivos de Calidad y el desempeño del SGC a todo el personal (interno y externo), y que se
                mantenga registro de esas comunicaciones. Esta pantalla es ese registro.
            </p>

            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-green-50 text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b bg-gray-50">
                                <th class="py-2 px-3">Fecha</th>
                                <th class="py-2 px-3">Qué se comunicó</th>
                                <th class="py-2 px-3">Canal</th>
                                <th class="py-2 px-3">A quién</th>
                                <th class="py-2 px-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($communications as $communication)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-3 whitespace-nowrap">{{ $communication->communicated_at->format('d-m-Y') }}</td>
                                    <td class="py-2 px-3">
                                        @foreach($communication->topicLabels() as $label)
                                            <span class="inline-block text-xs bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-full mr-1 mb-1">{{ $label }}</span>
                                        @endforeach
                                    </td>
                                    <td class="py-2 px-3 text-gray-600">{{ $communication->channel }}</td>
                                    <td class="py-2 px-3 text-gray-600">{{ $communication->audience }}</td>
                                    <td class="py-2 px-3 text-right whitespace-nowrap">
                                        <a href="{{ route('quality.internal-communications.edit', $communication) }}" class="text-gray-600 hover:underline text-xs font-medium">Editar</a>
                                        <span class="text-gray-300 mx-1">|</span>
                                        <form method="POST" action="{{ route('quality.internal-communications.destroy', $communication) }}"
                                              class="inline" onsubmit="return confirm('¿Eliminar este registro?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:underline text-xs font-medium">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-gray-500">
                                        Aún no hay comunicaciones registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $communications->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
