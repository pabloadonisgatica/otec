@props([
    'items' => [], // [['label'=>'Empresas','url'=>route(...)], ['label'=>'Crear']]
])

<nav class="mb-4 text-sm text-gray-600" aria-label="Breadcrumb">
    <ol class="flex flex-wrap items-center gap-1">
        <li>
            <a href="{{ route('dashboard') }}"
               class="text-gray-500 hover:text-gray-700">
                Inicio
            </a>
        </li>

        @foreach($items as $item)
            <li class="flex items-center gap-1">
                <span class="mx-1 text-gray-400">/</span>

                @if(isset($item['url']))
                    <a href="{{ $item['url'] }}"
                       class="text-gray-500 hover:text-gray-700">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="text-gray-800 font-medium">
                        {{ $item['label'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
