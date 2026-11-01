@php use App\Services\Questionnaires\Instruments\GroupEvaluationInstrument as GE; @endphp
<table style="border-collapse: collapse; width: 100%; border: 1px solid #eee;">
    @foreach ($result['dimensions'] ?? [] as $d)
        <tr>
            <td style="padding: 10px 12px; border-bottom: 1px solid #eee;">{{ $d['label'] }}</td>
            <td style="padding: 10px 12px; border-bottom: 1px solid #eee; text-align: right; white-space: nowrap;">
                <b>{{ $d['score'] }}</b> de {{ GE::SCORE_MAX }} &nbsp;
                <span style="background: {{ GE::LEVELS[$d['level']]['color'] }}; color: #fff; padding: 3px 10px; border-radius: 10px; font-size: 13px;">{{ $d['level_label'] }}</span>
            </td>
        </tr>
    @endforeach
</table>
