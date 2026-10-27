{{-- Indicador de pasos de la carga masiva. Uso: @include('participants.import._steps', ['current' => 2]) --}}
@php
    $steps = [1 => 'Subir archivo', 2 => 'Asignar columnas', 3 => 'Revisar e importar'];
@endphp

<ol class="mb-6 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm">
    @foreach ($steps as $number => $label)
        <li class="flex items-center gap-2 {{ $number === $current ? 'font-semibold text-indigo-700' : ($number < $current ? 'text-gray-700' : 'text-gray-400') }}">
            <span class="flex h-6 w-6 items-center justify-center rounded-full text-xs
                {{ $number === $current ? 'bg-indigo-600 text-white' : ($number < $current ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500') }}">
                {{ $number < $current ? '✓' : $number }}
            </span>
            {{ $label }}
        </li>
    @endforeach
</ol>
