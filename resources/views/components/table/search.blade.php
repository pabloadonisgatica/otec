@props([
    'action' => null,                 // URL destino del GET
    'name' => 'q',                    // nombre del parámetro
    'value' => '',                    // valor actual
    'placeholder' => 'Buscar…',
    'keep' => [],                     // params extra a mantener: ['status','region']
])

@php
    $action = $action ?: url()->current();

    // Mantener params existentes del request (excepto q y page)
    $keepFromRequest = request()->except([$name, 'page']);

    // Si el usuario pasa "keep", filtramos solo esos keys; si no, mantenemos todo
    $paramsToKeep = empty($keep) ? $keepFromRequest : array_intersect_key($keepFromRequest, array_flip($keep));
@endphp

<form method="GET" action="{{ $action }}" class="mb-4 flex flex-col sm:flex-row sm:items-center gap-2">
    {{-- Mantener filtros existentes --}}
    @foreach($paramsToKeep as $k => $v)
        @if(is_array($v))
            @foreach($v as $item)
                <input type="hidden" name="{{ $k }}[]" value="{{ $item }}">
            @endforeach
        @else
            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endif
    @endforeach

    <input
        type="text"
        name="{{ $name }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        class="w-full sm:w-96 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
        autocomplete="off"
    />

    <div class="flex items-center gap-2">
        <button type="submit" class="px-4 py-2 rounded bg-gray-800 text-white">
            Buscar
        </button>

        @if((string)$value !== '')
            <a href="{{ $action }}" class="px-3 py-2 rounded bg-gray-100 text-gray-800">
                Limpiar
            </a>
        @endif
    </div>
</form>
