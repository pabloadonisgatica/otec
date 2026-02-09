<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar plantilla</h2>

            <form action="{{ route('diploma-templates.destroy', $template) }}" method="POST"
                  onsubmit="return confirm('¿Eliminar plantilla?')" class="inline">
                @csrf
                @method('DELETE')
                <button class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-red-500">
                    Eliminar
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-green-50 text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <form action="{{ route('diploma-templates.update', $template) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        @include('diploma_templates._form', ['template' => $template])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
