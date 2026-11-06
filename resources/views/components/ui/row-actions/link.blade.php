{{--
    Acción visible de una fila (1 o 2 como máximo): botón secundario con texto.
    Con href es un enlace; sin href es un botón (por ejemplo x-on:click="open(...)"
    para abrir un modal de detalle).
--}}
@props(['href' => null])

@php
    $classes = 'inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 whitespace-nowrap';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="button" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
