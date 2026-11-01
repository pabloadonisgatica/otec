@php use App\Services\Questionnaires\Instruments\LeadershipInstrument as LI; @endphp
<table style="border-collapse: collapse; width: 100%; border: 1px solid #eee;">
    @foreach (LI::STYLES as $key => $style)
        <tr>
            <td style="padding: 8px 12px; border-bottom: 1px solid #eee;">
                <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: {{ $style['color'] }}; margin-right: 8px;"></span>{{ $style['label'] }}
            </td>
            <td style="padding: 8px 12px; border-bottom: 1px solid #eee; text-align: right;"><b>{{ $result['styles'][$key]['percent'] ?? 0 }}%</b></td>
        </tr>
    @endforeach
</table>
<p style="margin: 12px 0 0;">Tu flexibilidad para liderar es: <b>{{ $result['flexibility'] ?? '' }}</b></p>
