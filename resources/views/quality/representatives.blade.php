<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Representante de la Dirección</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[
                ['label' => 'Ver Norma', 'url' => route('quality.norm.index')],
                ['label' => 'Representante de la Dirección'],
            ]" />

            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-green-50 text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Representante actual --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6">

                <h3 class="font-semibold text-gray-900 mb-4">Representante actual</h3>

                @if($current)
                    <div class="border border-indigo-100 bg-indigo-50 rounded-lg p-4">
                        <p class="font-medium text-gray-900">{{ $current->name }}</p>
                        @if($current->position)
                            <p class="text-sm text-gray-600">{{ $current->position }}</p>
                        @endif
                        <p class="text-xs text-gray-500 mt-1">
                            Desde el {{ $current->start_date->format('d-m-Y') }}
                        </p>
                        @if($current->notes)
                            <p class="text-xs text-gray-500 mt-1">{{ $current->notes }}</p>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-gray-500">Aún no hay un representante asignado.</p>
                @endif

            </div>

            {{-- Asignar / cambiar --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6 mt-6">

                <h3 class="font-semibold text-gray-900 mb-4">
                    {{ $current ? 'Cambiar representante' : 'Asignar representante' }}
                </h3>

                @if($current)
                    <p class="text-xs text-gray-500 mb-4">
                        Al guardar, "{{ $current->name }}" pasará automáticamente al historial con la fecha de término correspondiente.
                    </p>
                @endif

                <form method="POST" action="{{ route('quality.representatives.store') }}" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Nombre</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="mt-1 w-full rounded-md border-gray-300 text-sm">
                            @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Cargo</label>
                            <input type="text" name="position" value="{{ old('position') }}" placeholder="Ej: Gerente General, Relator"
                                   class="mt-1 w-full rounded-md border-gray-300 text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Desde</label>
                        <input type="date" name="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}" required
                               class="mt-1 w-full rounded-md border-gray-300 text-sm">
                        @error('start_date') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Notas (opcional)</label>
                        <textarea name="notes" rows="2" class="mt-1 w-full rounded-md border-gray-300 text-sm">{{ old('notes') }}</textarea>
                    </div>

                    <div class="flex justify-end">
                        <button class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                            Guardar
                        </button>
                    </div>

                </form>

            </div>

            {{-- Historial --}}
            @if($history->isNotEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6 mt-6">

                    <h3 class="font-semibold text-gray-900 mb-4">Historial</h3>

                    <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b bg-gray-50">
                                <th class="py-2 px-3">Nombre</th>
                                <th class="py-2 px-3">Cargo</th>
                                <th class="py-2 px-3">Desde</th>
                                <th class="py-2 px-3">Hasta</th>
                                <th class="py-2 px-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($history as $representative)
                                <tr class="border-b">
                                    <td class="py-2 px-3">{{ $representative->name }}</td>
                                    <td class="py-2 px-3 text-gray-600">{{ $representative->position ?? '—' }}</td>
                                    <td class="py-2 px-3 text-gray-600 whitespace-nowrap">{{ $representative->start_date->format('d-m-Y') }}</td>
                                    <td class="py-2 px-3 text-gray-600 whitespace-nowrap">
                                        {{ $representative->end_date?->format('d-m-Y') ?? '—' }}
                                    </td>
                                    <td class="py-2 px-3 text-right">
                                        <form method="POST" action="{{ route('quality.representatives.destroy', $representative) }}"
                                              onsubmit="return confirm('¿Eliminar este registro del historial?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-xs text-red-600 hover:underline">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>

                </div>
            @endif

        </div>
    </div>
</x-app-layout>
