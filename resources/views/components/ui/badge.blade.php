{{--
    Chip de estado. Siempre con color: los tonos están definidos aquí (Tailwind
    solo genera clases que aparecen en las vistas) y su significado en
    App\Support\StatusBadge. En modo oscuro los reasigna resources/css/app.css.

    Uso: <x-ui.badge tone="success">Aprobado</x-ui.badge>
    Para estados de un modelo, mejor <x-ui.status-badge domain="execution" :status="$execution->status" />
--}}
@props(['tone' => 'info'])

@php
    $tones = [
        'success' => 'bg-green-100 text-green-800',
        'info' => 'bg-blue-50 text-blue-700',
        'new' => 'bg-purple-50 text-purple-700',
        'warning' => 'bg-amber-100 text-amber-800',
        'attention' => 'bg-orange-50 text-orange-700',
        'critical' => 'bg-red-100 text-red-800',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium whitespace-nowrap ' . ($tones[$tone] ?? $tones['info'])]) }}>
    {{ $slot }}
</span>
