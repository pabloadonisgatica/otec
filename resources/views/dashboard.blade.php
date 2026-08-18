<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard OTEC') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="text-lg font-semibold mb-4">Módulos</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                        <a href="{{ route('companies.index') }}" class="flex items-start gap-3 p-4 border rounded-lg hover:bg-gray-50">
                            <x-icon name="building" class="w-6 h-6 text-indigo-600 shrink-0" />
                            <div>
                                <div class="font-semibold">Empresas</div>
                                <div class="text-sm text-gray-600">Crear, editar y listar empresas</div>
                            </div>
                        </a>

                        <a href="{{ route('budgets.index') }}" class="flex items-start gap-3 p-4 border rounded-lg hover:bg-gray-50">
                            <x-icon name="currency" class="w-6 h-6 text-indigo-600 shrink-0" />
                            <div>
                                <div class="font-semibold">Presupuestos</div>
                                <div class="text-sm text-gray-600">Crear y administrar presupuestos</div>
                            </div>
                        </a>

                        <a href="{{ route('courses.index') }}" class="flex items-start gap-3 p-4 border rounded-lg hover:bg-gray-50">
                            <x-icon name="book-open" class="w-6 h-6 text-indigo-600 shrink-0" />
                            <div>
                                <div class="font-semibold">Cursos</div>
                                <div class="text-sm text-gray-600">Gestión de cursos y fechas</div>
                            </div>
                        </a>

                        <a href="{{ route('instructors.index') }}" class="flex items-start gap-3 p-4 border rounded-lg hover:bg-gray-50">
                            <x-icon name="academic-cap" class="w-6 h-6 text-indigo-600 shrink-0" />
                            <div>
                                <div class="font-semibold">Relatores</div>
                                <div class="text-sm text-gray-600">Registro y documentos</div>
                            </div>
                        </a>

                        <a href="{{ route('participants.index') }}" class="flex items-start gap-3 p-4 border rounded-lg hover:bg-gray-50">
                            <x-icon name="users" class="w-6 h-6 text-indigo-600 shrink-0" />
                            <div>
                                <div class="font-semibold">Participantes</div>
                                <div class="text-sm text-gray-600">Registro y listado</div>
                            </div>
                        </a>

                        <a href="{{ route('executions.index') }}" class="flex items-start gap-3 p-4 border rounded-lg hover:bg-gray-50">
                            <x-icon name="calendar-check" class="w-6 h-6 text-indigo-600 shrink-0" />
                            <div>
                                <div class="font-semibold">Ejecuciones</div>
                                <div class="text-sm text-gray-600">Planificación, agenda, asistencia y libro de clases</div>
                            </div>
                        </a>

                        <a href="{{ route('quality.norm.index') }}" class="flex items-start gap-3 p-4 border rounded-lg hover:bg-gray-50">
                            <x-icon name="shield-check" class="w-6 h-6 text-indigo-600 shrink-0" />
                            <div>
                                <div class="font-semibold">Gestión de Calidad</div>
                                <div class="text-sm text-gray-600">NCH 2728, encuestas y no conformidades</div>
                            </div>
                        </a>

                        <a href="{{ route('diplomas.index') }}" class="flex items-start gap-3 p-4 border rounded-lg hover:bg-gray-50">
                            <x-icon name="certificate" class="w-6 h-6 text-indigo-600 shrink-0" />
                            <div>
                                <div class="font-semibold">Diplomas</div>
                                <div class="text-sm text-gray-600">Plantilla y emisión con QR</div>
                            </div>
                        </a>

                        <a href="{{ route('settings.index') }}" class="flex items-start gap-3 p-4 border rounded-lg hover:bg-gray-50">
                            <x-icon name="cog" class="w-6 h-6 text-indigo-600 shrink-0" />
                            <div>
                                <div class="font-semibold">Configuración</div>
                                <div class="text-sm text-gray-600">Datos OTEC y usuarios</div>
                            </div>
                        </a>

                    </div>

                    <div class="mt-6 text-sm text-gray-500">
                        Sesión iniciada como <span class="font-medium">{{ auth()->user()->email }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
