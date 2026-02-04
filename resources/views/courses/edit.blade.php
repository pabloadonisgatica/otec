<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar curso
        </h2>
    </x-slot>

    @php
        $contents = old('contents', collect($course->contents)->map(fn($c) => [
            'activity' => $c->activity,
            'content' => $c->content,
            'hours_theoretical' => $c->hours_theoretical ?? 0,
            'hours_practical' => $c->hours_practical ?? 0,
            'hours_elearning' => $c->hours_elearning ?? 0,
        ])->values()->all());

        $initialCourseType = old('course_type', $course->course_type ?? 'privado');

        $initialModalities = old('instruction_modalities', $course->instruction_modalities ?? []);
        if (!is_array($initialModalities)) $initialModalities = [];

        $initialSenceApprovalDate = old('sence_approval_date', $course->sence_approval_date?->format('Y-m-d'));
    @endphp

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="$breadcrumbs" />

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">

                    <form method="POST"
                        action="{{ route('courses.update', $course) }}"
                        x-data="courseForm()"
                        x-init="
                            contents = @js($contents);
                            courseType = @js($initialCourseType);
                            modalities = @js($initialModalities);
                            senceApprovalDate = @js($initialSenceApprovalDate);
                            init();
                        "
                        class="space-y-6">
                        @csrf
                        @method('PUT')


                        @include('courses.partials.form', ['course' => $course])

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <a href="{{ route('courses.index') }}"
                               class="px-4 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
                                Cancelar
                            </a>

                            <button type="submit"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500">
                                Actualizar
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>

    <script>
        function courseForm() {
            return {
                contents: [],
                courseType: null,
                modalities: [],
                senceApprovalDate: null,

                init() {
                    if (!Array.isArray(this.contents)) this.contents = [];

                    if (this.contents.length === 0) {
                        this.contents.push({
                            activity: '',
                            content: '',
                            hours_theoretical: 0,
                            hours_practical: 0,
                            hours_elearning: 0,
                        });
                    }
                },

                get hasSelfLearning() {
                    return Array.isArray(this.modalities) && this.modalities.includes('distance_self');
                },

                get senceExpiryText() {
                    if (this.courseType !== 'sence') return '—';
                    if (!this.senceApprovalDate) return 'Ingrese fecha de aprobación';

                    const d = new Date(this.senceApprovalDate);
                    if (isNaN(d.getTime())) return 'Fecha inválida';

                    d.setFullYear(d.getFullYear() + 4);

                    const yyyy = d.getFullYear();
                    const mm = String(d.getMonth() + 1).padStart(2, '0');
                    const dd = String(d.getDate()).padStart(2, '0');
                    return `${dd}-${mm}-${yyyy}`;
                },

                toNum(v) {
                    const n = parseFloat(v);
                    return isNaN(n) ? 0 : n;
                },

                get totalTheoretical() {
                    return this.contents.reduce((sum, r) => sum + this.toNum(r.hours_theoretical), 0);
                },
                get totalPractical() {
                    return this.contents.reduce((sum, r) => sum + this.toNum(r.hours_practical), 0);
                },
                get totalElearning() {
                    return this.contents.reduce((sum, r) => sum + this.toNum(r.hours_elearning), 0);
                },
                get totalHours() {
                    return this.totalTheoretical + this.totalPractical + this.totalElearning;
                },

                addContent() {
                    this.contents.push({
                        activity: '',
                        content: '',
                        hours_theoretical: 0,
                        hours_practical: 0,
                        hours_elearning: 0,
                    });
                },

                removeContent(index) {
                    this.contents.splice(index, 1);
                },
            }
        }
    </script>
</x-app-layout>
