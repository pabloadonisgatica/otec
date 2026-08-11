@props([
    'title',
    'message'
])

<div class="py-16 text-center">

    <div class="text-lg font-semibold text-gray-900">
        {{ $title }}
    </div>

    <div class="mt-2 text-sm text-gray-500">
        {{ $message }}
    </div>

</div>