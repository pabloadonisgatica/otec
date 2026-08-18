@php
    $qualityTabs = [
        ['label' => 'Ver Norma', 'route' => 'quality.norm.index', 'active' => request()->routeIs('quality.norm.*')],
        ['label' => 'No Conformidades', 'route' => 'quality.non-conformities.index', 'active' => request()->routeIs('quality.non-conformities.*')],
        ['label' => 'Proveedores', 'route' => 'quality.providers.index', 'active' => request()->routeIs('quality.providers.*')],
    ];
@endphp

<div class="flex gap-1 mb-6 border-b border-gray-200 overflow-x-auto">
    @foreach($qualityTabs as $tab)
        <a href="{{ route($tab['route']) }}"
           class="px-4 py-2.5 border-b-2 text-sm whitespace-nowrap {{ $tab['active']
               ? 'border-indigo-600 text-indigo-600 font-semibold'
               : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>
