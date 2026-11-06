<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar evaluación del {{ $evaluation->evaluated_at->format('d-m-Y') }} — {{ $provider->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @include('quality._nav')

            <form method="POST" action="{{ route('quality.providers.evaluations.update', [$provider, $evaluation]) }}">
                @csrf
                @method('PUT')
                @include('quality.providers.evaluations._form')

                <div class="flex items-center justify-between mt-6">
                    <div class="flex items-center gap-3">
                        <button class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                            Guardar cambios
                        </button>
                        <a href="{{ route('quality.providers.show', $provider) }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                    </div>
                </div>
            </form>

            <form method="POST" action="{{ route('quality.providers.evaluations.destroy', [$provider, $evaluation]) }}"
                  onsubmit="return confirm('¿Eliminar esta evaluación? Esta acción no se puede deshacer.')" class="mt-4 text-right">
                @csrf
                @method('DELETE')
                <button class="text-xs text-red-600 hover:underline">Eliminar evaluación</button>
            </form>
        </div>
    </div>
</x-app-layout>
