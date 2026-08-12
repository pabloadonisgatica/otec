<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $provider->name }}
            </h2>

            <div class="flex items-center gap-3">
                <a href="{{ route('quality.providers.report', $provider) }}" target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-amber-500 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-amber-600">
                    📄 Enviar Informe (PDF)
                </a>

                <a href="{{ route('quality.providers.edit', $provider) }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-gray-50">
                    Editar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @include('quality._nav')

            @if (session('status'))
                <div class="p-3 rounded bg-green-50 text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6 space-y-4">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">RUT</p>
                        <p class="text-gray-900">{{ $provider->rut ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Categoría / Qué provee</p>
                        <p class="text-gray-900">{{ $provider->category ?? '—' }}</p>
                    </div>
                </div>

                @if($provider->business_name)
                    <div>
                        <p class="text-sm text-gray-500">Giro / Razón social</p>
                        <p class="text-gray-900">{{ $provider->business_name }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="text-gray-900">{{ $provider->email ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Teléfono</p>
                        <p class="text-gray-900">{{ $provider->phone ?? '—' }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Dirección</p>
                    <p class="text-gray-900">
                        {{ $provider->address ?? '—' }}
                        @if($provider->commune) — {{ $provider->commune }} @endif
                        @if($provider->region) , {{ $provider->region }} @endif
                    </p>
                </div>

                @if($provider->contact_name || $provider->contact_email || $provider->contact_phone)
                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-sm font-semibold text-gray-700 mb-2">Contacto principal</p>
                        <div class="grid grid-cols-3 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Nombre</p>
                                <p class="text-gray-900">{{ $provider->contact_name ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Email</p>
                                <p class="text-gray-900">{{ $provider->contact_email ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Teléfono</p>
                                <p class="text-gray-900">{{ $provider->contact_phone ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($provider->notes)
                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-sm text-gray-500">Notas</p>
                        <p class="text-gray-900 whitespace-pre-line">{{ $provider->notes }}</p>
                    </div>
                @endif

            </div>

            <a href="{{ route('quality.providers.index') }}" class="text-sm text-gray-600 hover:underline">
                ← Volver al listado
            </a>

        </div>
    </div>
</x-app-layout>
