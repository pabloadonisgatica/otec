<div {{ $attributes->merge([
    'class' => 'bg-white shadow-sm rounded-xl border border-gray-200'
]) }}>
    {{ $slot }}
</div>