@php
    $courseLines = $budget->courses->map(function($l){
        return [
            'id' => $l->id,
            'course_id' => $l->course_id,
            'participants' => $l->participants,
            'hours' => $l->hours,
            'unit_price' => $l->unit_price,
            'discount_percent' => $l->discount_percent,
            'instructor_id' => $l->instructor_id,
        ];
    })->values();
@endphp

<div x-data="{
        lines: {{ $courseLines->toJson() }},
        add() {
            this.lines.push({
                id: null,
                course_id: '',
                participants: 0,
                hours: 0,
                unit_price: 0,
                discount_percent: '',
                instructor_id: ''
            });
        },
        remove(idx) {
            this.lines.splice(idx, 1);
        },
        lineTotal(l){
            let total = (parseInt(l.participants||0) * parseInt(l.unit_price||0));
            let d = l.discount_percent === '' || l.discount_percent === null ? 0 : parseInt(l.discount_percent||0);
            if (d > 0) total = Math.round(total * (1 - d/100));
            return total;
        }
     }">

    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-gray-600">
            Agrega uno o más cursos y asigna relator desde base de datos.
        </p>

        <button type="button"
                class="inline-flex items-center px-3 py-2 bg-gray-700 text-white rounded-md text-xs font-semibold uppercase hover:bg-gray-600"
                @click="add()">
            + Agregar curso
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left border-b">
                    <th class="py-2">Curso</th>
                    <th class="py-2 w-28">Participantes</th>
                    <th class="py-2 w-20">Horas</th>
                    <th class="py-2 w-36">Valor / part.</th>
                    <th class="py-2 w-20">% Desc.</th>
                    <th class="py-2">Relator</th>
                    <th class="py-2 text-right w-36">Total</th>
                    <th class="py-2 text-right w-24">Acción</th>
                </tr>
            </thead>

            <tbody>
                <template x-for="(l, idx) in lines" :key="idx">
                    <tr class="border-b">
                        <td class="py-2 pr-2">
                            <input type="hidden" :name="`courses[${idx}][id]`" x-model="l.id">

                            <select class="w-full rounded border-gray-300"
                                    :name="`courses[${idx}][course_id]`"
                                    x-model="l.course_id" required>
                                <option value="">Seleccione…</option>
                                @foreach ($courses as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </td>

                        <td class="py-2 pr-2">
                            <input type="number" min="0"
                                   class="w-full rounded border-gray-300"
                                   :name="`courses[${idx}][participants]`"
                                   x-model="l.participants">
                        </td>

                        <td class="py-2 pr-2">
                            <input type="number" min="0"
                                   class="w-full rounded border-gray-300"
                                   :name="`courses[${idx}][hours]`"
                                   x-model="l.hours">
                        </td>

                        <td class="py-2 pr-2">
                            <input type="number" min="0"
                                   class="w-full rounded border-gray-300"
                                   :name="`courses[${idx}][unit_price]`"
                                   x-model="l.unit_price">
                        </td>

                        <td class="py-2 pr-2">
                            <input type="number" min="0" max="100"
                                   class="w-full rounded border-gray-300"
                                   :name="`courses[${idx}][discount_percent]`"
                                   x-model="l.discount_percent">
                        </td>

                        <td class="py-2 pr-2">
                            <select class="w-full rounded border-gray-300"
                                    :name="`courses[${idx}][instructor_id]`"
                                    x-model="l.instructor_id">
                                <option value="">—</option>
                                @foreach ($instructors as $r)
                                    <option value="{{ $r->id }}">
                                        {{ $r->name ?? ($r->first_name.' '.$r->last_name) }}
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        <td class="py-2 text-right font-medium">
                            $<span x-text="lineTotal(l).toLocaleString('es-CL')"></span>
                        </td>

                        <td class="py-2 text-right whitespace-nowrap">
                            <button type="button"
                                    class="underline text-sm text-red-600 hover:text-red-800"
                                    @click="remove(idx)">
                                Quitar
                            </button>
                        </td>
                    </tr>
                </template>

                <template x-if="lines.length === 0">
                    <tr>
                        <td colspan="8" class="py-6 text-center text-gray-500">
                            Agrega al menos un curso para comenzar.
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>
