{{--
    Opción del menú "⋯" de una fila.
    - Con href: enlace (GET).
    - Con action: formulario (POST por defecto; method="DELETE"/"PUT" para otros).
    - Sin href ni action: botón (por ejemplo x-on:click="open(...)" para un modal).
    - danger: texto rojo, para acciones destructivas (van al final, tras un divider).
    - confirm: mensaje de confirmación antes de enviar.
--}}
@props([
    'href' => null,
    'action' => null,
    'method' => 'POST',
    'danger' => false,
    'confirm' => null,
])

@php
    $classes = 'block w-full px-4 py-2 text-left text-sm focus:outline-none '
        . ($danger ? 'text-red-600 hover:bg-red-50 focus:bg-red-50' : 'text-gray-700 hover:bg-gray-100 focus:bg-gray-100');
    $method = strtoupper($method);
@endphp

@if ($action)
    <form method="POST" action="{{ $action }}"
          @if ($confirm) onsubmit="return confirm(@js($confirm))" @endif>
        @csrf
        @unless ($method === 'POST')
            @method($method)
        @endunless
        <button type="submit" role="menuitem" {{ $attributes->merge(['class' => $classes]) }}>
            {{ $slot }}
        </button>
    </form>
@elseif ($href)
    <a href="{{ $href }}" role="menuitem" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="button" role="menuitem" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
