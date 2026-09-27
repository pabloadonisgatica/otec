<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Configuración
            </h2>
        </div>
    </x-slot>

    <div class="py-6" x-data="{ tab: '{{ session('settings_tab', $errors->hasAny(['signer_name', 'signer_title', 'form_code', 'signature', 'logo_name', 'logo_file']) ? 'diplomas' : 'logo') }}' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @isset($breadcrumbs)
                <x-breadcrumb :items="$breadcrumbs" />
            @endisset

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">

                    {{-- Tabs --}}
                    <div class="border-b border-gray-200 mb-6">
                        <nav class="-mb-px flex gap-6">
                            <button type="button"
                                    class="py-2 px-1 border-b-2 text-sm font-medium"
                                    :class="tab === 'logo' ? 'border-gray-800 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    @click="tab = 'logo'">
                                Información OTEC (Logo)
                            </button>

                            <button type="button"
                                    class="py-2 px-1 border-b-2 text-sm font-medium"
                                    :class="tab === 'diplomas' ? 'border-gray-800 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    @click="tab = 'diplomas'">
                                Diplomas
                            </button>

                            <button type="button"
                                    class="py-2 px-1 border-b-2 text-sm font-medium"
                                    :class="tab === 'users' ? 'border-gray-800 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    @click="tab = 'users'">
                                Gestión de usuarios
                            </button>
                        </nav>
                    </div>

                    {{-- TAB: Logo --}}
                    <div x-show="tab === 'logo'" x-cloak>
                        <h3 class="text-lg font-semibold text-gray-800">Logo OTEC</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Sube el logo que se mostrará en la navegación del sistema.
                        </p>

                        @if (session('status'))
                            <div class="mt-4 p-3 rounded bg-green-50 text-green-700 text-sm">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form class="mt-6 space-y-4" action="{{ route('settings.logo.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Subir logo</label>
                                <input type="file" name="logo"
                                       class="mt-2 block w-full text-sm text-gray-700
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-md file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-gray-800 file:text-white
                                              hover:file:bg-gray-700" />
                                @error('logo')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-xs text-gray-500">PNG/JPG/WEBP, máximo 2MB.</p>
                            </div>

                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Guardar logo
                            </button>
                        </form>

                        <form class="mt-8 pt-8 border-t border-gray-200 space-y-4" action="{{ route('settings.otec-name.update') }}" method="POST">
                            @csrf

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nombre de la OTEC</label>
                                <input type="text" name="otec_name"
                                       value="{{ old('otec_name', \App\Models\AppSetting::get('otec_name')) }}"
                                       placeholder="Ej: Proyecto Humano Capacitación Limitada"
                                       class="mt-2 block w-full rounded-md border-gray-300 shadow-sm" />
                                @error('otec_name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-xs text-gray-500">Aparece en documentos generados, como el Libro de Control de Clases.</p>
                            </div>

                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Guardar nombre
                            </button>
                        </form>
                    </div>

                    {{-- TAB: Diplomas --}}
                    <div x-show="tab === 'diplomas'" x-cloak>
                        @php
                            $diplomaSubtab = session('settings_subtab', $errors->hasAny(['logo_name', 'logo_file']) ? 'logos' : 'firma');
                        @endphp

                        <div x-data="{ subtab: '{{ $diplomaSubtab }}' }">

                            <div class="inline-flex rounded-lg bg-gray-100 p-1">
                                <button type="button"
                                        class="px-4 py-1.5 text-sm font-medium rounded-md"
                                        :class="subtab === 'firma' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                                        @click="subtab = 'firma'">
                                    Firma y código
                                </button>
                                <button type="button"
                                        class="px-4 py-1.5 text-sm font-medium rounded-md"
                                        :class="subtab === 'logos' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                                        @click="subtab = 'logos'">
                                    Logos
                                </button>
                            </div>


                        @if (session('status'))
                            <div class="mt-4 p-3 rounded bg-green-50 text-green-700 text-sm">
                                {{ session('status') }}
                            </div>
                        @endif

                            {{-- Sub-tab: Firma y código --}}
                            <div x-show="subtab === 'firma'" x-cloak class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-800">Datos del diploma</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Firmante, firma con timbre y código del formulario. Se usan en todas las plantillas de diploma.
                        </p>
                        <form class="mt-6 space-y-5 max-w-2xl" action="{{ route('settings.diplomas.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nombre del firmante</label>
                                    <input type="text" name="signer_name"
                                           value="{{ old('signer_name', $diploma['signer_name']) }}"
                                           placeholder="Ej: Manuel Bustamante Vásquez"
                                           class="mt-2 block w-full rounded-md border-gray-300 shadow-sm" />
                                    @error('signer_name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Cargo</label>
                                    <input type="text" name="signer_title"
                                           value="{{ old('signer_title', $diploma['signer_title']) }}"
                                           placeholder="Ej: Gerente"
                                           class="mt-2 block w-full rounded-md border-gray-300 shadow-sm" />
                                    @error('signer_title')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Código del formulario</label>
                                <input type="text" name="form_code"
                                       value="{{ old('form_code', $diploma['form_code']) }}"
                                       placeholder="Ej: F38-Rev.00"
                                       class="mt-2 block w-full sm:w-64 rounded-md border-gray-300 shadow-sm" />
                                @error('form_code')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-xs text-gray-500">Código del registro de calidad. Aparece al pie del diploma.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Firma y timbre</label>

                                @if ($diploma['signature_path'])
                                    <div class="mt-2 flex items-end gap-4">
                                        <div class="rounded-md border border-gray-200 p-2"
                                             style="background-image: repeating-conic-gradient(#f3f4f6 0% 25%, #fff 0% 50%); background-size: 16px 16px;">
                                            <img src="{{ asset('storage/' . $diploma['signature_path']) }}"
                                                 alt="Firma actual" class="h-32 w-auto">
                                        </div>
                                        <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                                            <input type="checkbox" name="remove_signature" value="1" class="rounded border-gray-300">
                                            Quitar firma
                                        </label>
                                    </div>
                                @endif

                                <input type="file" name="signature" accept="image/png,image/webp"
                                       class="mt-3 block w-full text-sm text-gray-700
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-md file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-gray-800 file:text-white
                                              hover:file:bg-gray-700" />
                                @error('signature')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-xs text-gray-500">PNG o WEBP con fondo transparente, máximo 2MB. Si subes una nueva, reemplaza la actual.</p>
                            </div>

                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Guardar datos del diploma
                            </button>
                        </form>
                            </div>

                            {{-- Sub-tab: Logos --}}
                            <div x-show="subtab === 'logos'" x-cloak class="mt-6 max-w-2xl">
                            <h3 class="text-lg font-semibold text-gray-800">Logos del diploma</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Cada diploma lleva máximo 2 logos: el de la OTEC (siempre) y, si quieres, uno adicional que eliges al emitir.
                                Aquí puedes cargar todos los logos adicionales que necesites (ej: PACCAR, SENCE).
                            </p>

                            <div class="mt-5 flex items-center gap-4 rounded-md border border-gray-200 p-3">
                                <div class="flex h-14 w-28 items-center justify-center rounded bg-gray-50">
                                    @if ($appLogo)
                                        <img src="{{ asset('storage/' . $appLogo) }}" alt="Logo OTEC" class="max-h-12 max-w-24 object-contain">
                                    @else
                                        <span class="text-xs text-gray-400">Sin logo</span>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">Logo 1 · OTEC</p>
                                    <p class="text-xs text-gray-500">Va en todos los diplomas. Se cambia en la pestaña "Información OTEC (Logo)".</p>
                                </div>
                            </div>

                            <h4 class="mt-8 text-sm font-semibold text-gray-800">Logo 2 · Adicionales disponibles</h4>

                            @if ($diplomaLogos->isEmpty())
                                <p class="mt-2 text-sm text-gray-500">Aún no hay logos adicionales.</p>
                            @else
                                <ul class="mt-2 divide-y divide-gray-100 rounded-md border border-gray-200">
                                    @foreach ($diplomaLogos as $logo)
                                        <li class="flex items-center gap-4 p-3">
                                            <div class="flex h-14 w-28 items-center justify-center rounded bg-gray-50">
                                                <img src="{{ $logo->url() }}" alt="{{ $logo->name }}" class="max-h-12 max-w-24 object-contain">
                                            </div>
                                            <span class="flex-1 text-sm font-medium text-gray-800">{{ $logo->name }}</span>
                                            <form action="{{ route('settings.diploma-logos.destroy', $logo) }}" method="POST"
                                                  onsubmit="return confirm('¿Quitar el logo {{ $logo->name }} de la lista? Los diplomas ya emitidos lo conservan.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm text-red-600 hover:underline">Quitar</button>
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            <form class="mt-6 space-y-4" action="{{ route('settings.diploma-logos.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nombre <span class="font-normal text-gray-400">(opcional)</span></label>
                                        <input type="text" name="logo_name" value="{{ old('logo_name') }}"
                                               placeholder="Ej: PACCAR"
                                               class="mt-2 block w-full rounded-md border-gray-300 shadow-sm" />
                                        @error('logo_name')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Imagen</label>
                                        <input type="file" name="logo_file" accept="image/png,image/jpeg,image/webp"
                                               class="mt-2 block w-full text-sm text-gray-700
                                                      file:mr-4 file:py-2 file:px-4
                                                      file:rounded-md file:border-0
                                                      file:text-sm file:font-semibold
                                                      file:bg-gray-800 file:text-white
                                                      hover:file:bg-gray-700" />
                                        @error('logo_file')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500">PNG (idealmente con fondo transparente), JPG o WEBP, máximo 2MB. Si no escribes un nombre, se usa el del archivo.</p>

                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                    Agregar logo
                                </button>
                            </form>
                        </div>
                        </div>
                    </div>

                    {{-- TAB: Usuarios --}}
                    <div x-show="tab === 'users'" x-cloak>
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Usuarios</h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    Administra usuarios, roles y permisos por módulo.
                                </p>
                            </div>

                            <a href="{{ route('settings.users.create') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Nuevo usuario
                            </a>
                        </div>

                        <div class="mt-6 overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="text-left text-gray-600 border-b">
                                    <tr>
                                        <th class="py-2 pr-4">Nombre</th>
                                        <th class="py-2 pr-4">Email</th>
                                        <th class="py-2 pr-4">Rol</th>
                                        <th class="py-2 pr-4">Permisos</th>
                                        <th class="py-2 text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @forelse($users as $u)
                                        <tr>
                                            <td class="py-3 pr-4 font-medium text-gray-800">{{ $u->name }}</td>
                                            <td class="py-3 pr-4 text-gray-700">{{ $u->email }}</td>
                                            <td class="py-3 pr-4">
                                                <span class="inline-flex px-2 py-1 rounded text-xs font-semibold
                                                    {{ $u->role === 'admin' ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-700' }}">
                                                    {{ $u->role === 'admin' ? 'Administrador' : 'Usuario' }}
                                                </span>
                                            </td>
                                            <td class="py-3 pr-4 text-gray-600">
                                                @if($u->role === 'admin')
                                                    Todos
                                                @else
                                                    {{ $u->permissions ? implode(', ', $u->permissions) : '-' }}
                                                @endif
                                            </td>
                                            <td class="py-3 text-right">
                                                <a href="{{ route('settings.users.edit', $u) }}"
                                                   class="text-gray-800 hover:underline">
                                                    Editar
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-6 text-center text-gray-500">
                                                No hay usuarios registrados.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $users->links() }}
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
