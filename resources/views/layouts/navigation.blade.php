<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-16">
            <!-- Logo -->
            <div class="shrink-0 flex items-center">
                <a href="{{ route('dashboard') }}">
                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                </a>
            </div>

            <!-- Navigation Links (centradas) -->
            <div class="hidden sm:flex flex-1 justify-center">
                <div class="flex items-center h-16 gap-1">
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                        <x-icon name="dashboard" class="w-4 h-4" />
                        {{ __('Dashboard') }}
                    </a>

                    @php
                        $comercialActive = request()->routeIs('companies.*') || request()->routeIs('budgets.*');
                        $personasActive = request()->routeIs('instructors.*') || request()->routeIs('participants.*');
                        $academicoActive = request()->routeIs('courses.*') || request()->routeIs('executions.*') || request()->routeIs('diplomas.*') || request()->routeIs('diploma-templates.*');
                    @endphp

                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <button type="button" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out focus:outline-none {{ $comercialActive ? 'bg-indigo-50 text-indigo-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                                <x-icon name="building" class="w-4 h-4" />
                                {{ __('Comercial') }}
                                <x-icon name="chevron-down" class="w-3.5 h-3.5" />
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('companies.index')">{{ __('Empresas') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('budgets.index')">{{ __('Presupuestos') }}</x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <button type="button" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out focus:outline-none {{ $personasActive ? 'bg-indigo-50 text-indigo-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                                <x-icon name="users" class="w-4 h-4" />
                                {{ __('Personas') }}
                                <x-icon name="chevron-down" class="w-3.5 h-3.5" />
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('instructors.index')">{{ __('Relatores') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('participants.index')">{{ __('Participantes') }}</x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <button type="button" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out focus:outline-none {{ $academicoActive ? 'bg-indigo-50 text-indigo-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                                <x-icon name="book-open" class="w-4 h-4" />
                                {{ __('Académico') }}
                                <x-icon name="chevron-down" class="w-3.5 h-3.5" />
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('courses.index')">{{ __('Cursos') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('executions.index')">{{ __('Ejecuciones') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('diplomas.index')">{{ __('Diplomas') }}</x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    <a href="{{ route('quality.norm.index') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('quality.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                        <x-icon name="shield-check" class="w-4 h-4" />
                        {{ __('Calidad') }}
                    </a>
                </div>
            </div>

            <!-- Perfil (Configuración + Profile + Logout) -->
            <div class="hidden sm:flex sm:items-center">

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('settings.index')">
                            <span class="inline-flex items-center gap-2">
                                <x-icon name="cog" class="w-4 h-4" />
                                {{ __('Configuración') }}
                            </span>
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="flex items-center gap-2">
                <x-icon name="dashboard" class="w-4 h-4" /> {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-3 pb-2 border-t border-gray-200">
            <div class="px-4 text-xs font-semibold uppercase text-gray-400">{{ __('Comercial') }}</div>
            <div class="mt-1 space-y-1">
                <x-responsive-nav-link :href="route('companies.index')" :active="request()->routeIs('companies.*')" class="flex items-center gap-2">
                    <x-icon name="building" class="w-4 h-4" /> {{ __('Empresas') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('budgets.index')" :active="request()->routeIs('budgets.*')" class="flex items-center gap-2">
                    <x-icon name="currency" class="w-4 h-4" /> {{ __('Presupuestos') }}
                </x-responsive-nav-link>
            </div>
        </div>

        <div class="pt-3 pb-2 border-t border-gray-200">
            <div class="px-4 text-xs font-semibold uppercase text-gray-400">{{ __('Personas') }}</div>
            <div class="mt-1 space-y-1">
                <x-responsive-nav-link :href="route('instructors.index')" :active="request()->routeIs('instructors.*')" class="flex items-center gap-2">
                    <x-icon name="academic-cap" class="w-4 h-4" /> {{ __('Relatores') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('participants.index')" :active="request()->routeIs('participants.*')" class="flex items-center gap-2">
                    <x-icon name="users" class="w-4 h-4" /> {{ __('Participantes') }}
                </x-responsive-nav-link>
            </div>
        </div>

        <div class="pt-3 pb-2 border-t border-gray-200">
            <div class="px-4 text-xs font-semibold uppercase text-gray-400">{{ __('Académico') }}</div>
            <div class="mt-1 space-y-1">
                <x-responsive-nav-link :href="route('courses.index')" :active="request()->routeIs('courses.*')" class="flex items-center gap-2">
                    <x-icon name="book-open" class="w-4 h-4" /> {{ __('Cursos') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('executions.index')" :active="request()->routeIs('executions.*')" class="flex items-center gap-2">
                    <x-icon name="calendar-check" class="w-4 h-4" /> {{ __('Ejecuciones') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('diplomas.index')" :active="request()->routeIs('diplomas.*') || request()->routeIs('diploma-templates.*')" class="flex items-center gap-2">
                    <x-icon name="certificate" class="w-4 h-4" /> {{ __('Diplomas') }}
                </x-responsive-nav-link>
            </div>
        </div>

        <div class="pt-3 pb-2 border-t border-gray-200">
            <div class="mt-1 space-y-1">
                <x-responsive-nav-link :href="route('quality.norm.index')" :active="request()->routeIs('quality.*')" class="flex items-center gap-2">
                    <x-icon name="shield-check" class="w-4 h-4" /> {{ __('Calidad') }}
                </x-responsive-nav-link>
            </div>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('settings.index')" :active="request()->routeIs('settings.*')" class="flex items-center gap-2">
                    <x-icon name="cog" class="w-4 h-4" /> {{ __('Configuración') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
