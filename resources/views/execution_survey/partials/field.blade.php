@php
    $value = old($old_key, '');
@endphp

<div>
    <p class="text-sm font-medium text-gray-900 mb-3">
        {{ $field->label }}
        @if ($field->required)
            <span class="text-red-500">*</span>
        @endif
    </p>

    @if ($field->type === 'input')
        <input
            type="text"
            name="{{ $name }}"
            value="{{ $value }}"
            class="w-full rounded-lg border-gray-300">

    @elseif ($field->type === 'textarea')
        <textarea
            name="{{ $name }}"
            rows="4"
            class="w-full rounded-lg border-gray-300">{{ $value }}</textarea>

    @elseif ($field->type === 'select')
        <select name="{{ $name }}" class="w-full rounded-lg border-gray-300">
            <option value="">Seleccione...</option>
            @foreach (($field->options ?? []) as $option)
                <option value="{{ $option }}" {{ $value === $option ? 'selected' : '' }}>
                    {{ $option }}
                </option>
            @endforeach
        </select>

    @elseif ($field->type === 'radio')
        <div class="grid grid-cols-4 sm:grid-cols-8 gap-2" x-data="{ value: '{{ $value }}' }">
            @foreach (($field->options ?? []) as $option)
                <label
                    class="flex flex-col items-center gap-1 border rounded-lg py-2 text-xs cursor-pointer transition"
                    :class="value === '{{ $option }}'
                        ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                        : 'border-gray-300 hover:bg-gray-50'">

                    <input
                        type="radio"
                        name="{{ $name }}"
                        value="{{ $option }}"
                        x-model="value"
                        class="h-4 w-4">

                    {{ $option }}
                </label>
            @endforeach
        </div>
    @endif

    @error($old_key)
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>
