{{--
    Chip del estado de un modelo: texto en español y color según App\Support\StatusBadge.
    Uso: <x-ui.status-badge domain="execution" :status="$execution->status" />
--}}
@props(['domain', 'status'])

@php
    $badge = \App\Support\StatusBadge::for($domain, $status);
@endphp

<x-ui.badge :tone="$badge['tone']" {{ $attributes }}>{{ $badge['label'] }}</x-ui.badge>
