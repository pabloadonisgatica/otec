<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

    <x-ui.stat-card
        title="Participantes"
        :value="$execution->participants->count()"
        description="Participantes asociados" />

    <x-ui.stat-card
        title="Relatores"
        :value="$execution->instructors->count()"
        description="Relatores asociados" />

    <x-ui.stat-card
        title="Horas programadas"
        :value="$execution->sessions->sum('hours')"
        description="Horas calendarizadas" />

</div>