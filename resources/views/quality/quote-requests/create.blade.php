<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva Solicitud de Cotización</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @include('quality._nav')

            <form method="POST" action="{{ route('quality.quote-requests.store') }}" enctype="multipart/form-data">
                @csrf
                @include('quality.quote-requests._form')

                <div class="flex items-center gap-3 mt-6">
                    <button class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                        Registrar solicitud
                    </button>
                    <a href="{{ url()->previous() }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
