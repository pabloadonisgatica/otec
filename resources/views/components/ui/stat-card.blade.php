@props([
'title',
'value',
'icon' => null,
'color' => 'indigo'
])

<x-ui.card class="p-6">

    <div class="flex items-center justify-between">

        <div>

            <p class="text-sm text-gray-500">
                {{ $title }}
            </p>

            <h3 class="mt-2 text-3xl font-bold text-gray-900">
                {{ $value }}
            </h3>

        </div>

        @if($icon)
        <div class="text-{{ $color }}-600">
            {{ $icon }}
        </div>
        @endif

    </div>

</x-ui.card>