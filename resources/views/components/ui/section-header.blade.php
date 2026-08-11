@props([
    'title',
    'subtitle' => null,
    'actions' => null,
])

<div class="flex items-start justify-between px-6 py-5 border-b border-gray-200">

    <div>

        <h2 class="text-lg font-semibold text-gray-900">
            {{ $title }}
        </h2>

        @if($subtitle)
            <p class="mt-1 text-sm text-gray-500">
                {{ $subtitle }}
            </p>
        @endif

    </div>

    @if($actions)
        <div>
            {{ $actions }}
        </div>
    @endif

</div>