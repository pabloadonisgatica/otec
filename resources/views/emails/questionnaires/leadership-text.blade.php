@foreach ($result['styles'] ?? [] as $style)
{{ $style['label'] }}: {{ $style['percent'] }}%
@endforeach

Tu flexibilidad para liderar es: {{ $result['flexibility'] ?? '' }}
