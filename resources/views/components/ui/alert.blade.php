{{--
    Mensajes flash de toda la plataforma. Es el ÚNICO lugar donde se muestran:
    las vistas no deben repetir @if (session('status')).

    Claves: status / success (verde) y error (rojo).
    Se ignoran los códigos internos de Breeze (ej: "profile-updated"),
    que las vistas de perfil y login muestran con su propio texto.
--}}
@php
    $breezeCodes = ['profile-updated', 'password-updated', 'verification-link-sent'];

    $styles = [
        'success' => [
            'box' => 'border-green-200 bg-green-50',
            'text' => 'text-green-800',
            'icon' => 'text-green-600',
            'close' => 'text-green-600 hover:text-green-800',
            'path' => 'M5 13l4 4L19 7',
        ],
        'error' => [
            'box' => 'border-red-200 bg-red-50',
            'text' => 'text-red-800',
            'icon' => 'text-red-600',
            'close' => 'text-red-600 hover:text-red-800',
            'path' => 'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z',
        ],
    ];

    $messages = collect([
        ['type' => 'success', 'text' => session('status')],
        ['type' => 'success', 'text' => session('success')],
        ['type' => 'error', 'text' => session('error')],
    ])->filter(fn ($m) => filled($m['text']) && ! in_array($m['text'], $breezeCodes, true))
      ->unique('text');
@endphp

@foreach ($messages as $message)
    @php($style = $styles[$message['type']])

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
        <div
            x-data="{ show: true }"
            x-show="show"
            x-transition
            class="rounded-lg border {{ $style['box'] }} p-4 flex items-center justify-between">

            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 {{ $style['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $style['path'] }}"/>
                </svg>
                <span class="{{ $style['text'] }}">{{ $message['text'] }}</span>
            </div>

            <button type="button" @click="show = false" class="{{ $style['close'] }}">✕</button>
        </div>
    </div>
@endforeach
