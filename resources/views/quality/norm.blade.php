<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestión de Calidad — NCH 2728:2015</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @include('quality._nav')

            <p class="text-sm text-gray-500 mb-6">
                Mapa de la norma NCH 2728:2015. Los ítems con 🟢 ya están implementados y enlazan al módulo real —
                los marcados ⚪ todavía están pendientes de desarrollo.
            </p>

            <div class="space-y-6">

                @foreach($sections as $section)

                    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">

                        <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
                            <h3 class="font-semibold text-gray-800">{{ $section['title'] }}</h3>
                        </div>

                        <div class="divide-y divide-gray-100">
                            @foreach($section['items'] as $item)

                                @php
                                    $isLinked = in_array($item['status'], ['done', 'partial']) && isset($item['route']);
                                @endphp

                                <div class="flex items-center justify-between px-6 py-3">

                                    <div>
                                        <p class="text-sm text-gray-900">{{ $item['label'] }}</p>
                                        @if(!empty($item['note']))
                                            <p class="text-xs text-gray-400">{{ $item['note'] }}</p>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-2">

                                        @if(!empty($item['chip']))
                                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full whitespace-nowrap">
                                                {{ $item['chip'] }}
                                            </span>
                                        @endif

                                        @if($isLinked)
                                            <a href="{{ route($item['route']) }}"
                                               class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium whitespace-nowrap
                                                   {{ $item['status'] === 'done' ? 'bg-green-50 text-green-700 hover:bg-green-100' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">
                                                {{ $item['status'] === 'done' ? '🟢' : '🟡' }} Ver
                                            </a>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-400 whitespace-nowrap">
                                                ⚪ Pendiente
                                            </span>
                                        @endif

                                    </div>

                                </div>

                            @endforeach
                        </div>

                    </div>

                @endforeach

            </div>

        </div>
    </div>
</x-app-layout>
