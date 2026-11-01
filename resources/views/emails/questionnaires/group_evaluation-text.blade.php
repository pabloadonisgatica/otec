@foreach ($result['dimensions'] ?? [] as $d)
{{ $d['label'] }}: {{ $d['score'] }} de 40 ({{ $d['level_label'] }})
@endforeach
