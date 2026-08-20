<nav x-data="{ open: false }" class="bg-white border-b-4 border-brick-red shadow-sm">
    <!-- Barra superior negra -->
    <div class="bg-brick-black">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex justify-end gap-4 text-xs">
            @auth
                <span class="text-brick-yellow font-medium">Hola, {{ Auth::user()->name }}</span>
            @else
                <a href="{{ route('login') }}" class="text-gray-200 hover:text-brick-yellow transition">{{ __('Iniciar sesión') }}</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="text-gray-200 hover:text-brick-yellow transition">{{ __('Registrarse') }}</a>
                @endif
            @endauth
        </div>
    </div>

    <!-- Barra principal amarilla -->
    <div class="bg-brick-yellow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center">
                    <a href="{{ route('tienda.index') }}" class="flex items-center gap-2">
                        <img src="{{ asset('imagenes/logobricks.png') }}" alt="Brick Store" class="h-14 w-auto">
                    </a>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-1 sm:ms-10 sm:flex">
                        <a href="{{ route('tienda.index') }}"
                           class="inline-flex items-center px-4 py-2 text-sm font-bold uppercase tracking-wide rounded-md transition
                                  {{ request()->routeIs('tienda.*') ? 'bg-brick-black text-brick-yellow' : 'text-brick-black hover:bg-black/10' }}">
                            {{ __('Tienda') }}
                        </a>

                        @auth
                            <a href="{{ route('carrito.index') }}"
                               class="inline-flex items-center px-4 py-2 text-sm font-bold uppercase tracking-wide rounded-md transition
                                      {{ request()->routeIs('carrito.*') ? 'bg-brick-black text-brick-yellow' : 'text-brick-black hover:bg-black/10' }}">
                                {{ __('Carrito') }}
                            </a>

                            @if(Auth::user()->esAdmin())
                                <a href="{{ route('categorias.index') }}"
                                   class="inline-flex items-center px-4 py-2 text-sm font-bold uppercase tracking-wide rounded-md transition
                                          {{ request()->routeIs('categorias.*') ? 'bg-brick-black text-brick-yellow' : 'text-brick-black hover:bg-black/10' }}">
                                    {{ __('Categorías') }}
                                </a>
                                <a href="{{ route('productos.index') }}"
                                   class="inline-flex items-center px-4 py-2 text-sm font-bold uppercase tracking-wide rounded-md transition
                                          {{ request()->routeIs('productos.*') ? 'bg-brick-black text-brick-yellow' : 'text-brick-black hover:bg-black/10' }}">
                                    {{ __('Productos') }}
                                </a>
                                <a href="{{ route('reportes.index') }}"
                                   class="inline-flex items-center px-4 py-2 text-sm font-bold uppercase tracking-wide rounded-md transition
                                          {{ request()->routeIs('reportes.*') ? 'bg-brick-black text-brick-yellow' : 'text-brick-black hover:bg-black/10' }}">
                                    {{ __('Reportes') }}
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>

                <!-- Settings Dropdown -->
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    @auth
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border-2 border-brick-black text-sm font-bold uppercase rounded-md text-brick-black bg-white hover:bg-brick-black hover:text-brick-yellow focus:outline-none transition ease-in-out duration-150">
                                    <div>{{ Str::limit(Auth::user()->name, 15) }}</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('Profile') }}
                                </x-dropdown-link>

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
                    @endauth
                </div>

                <!-- Hamburger -->
                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-brick-black hover:bg-black/10 focus:outline-none transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-brick-yellow border-t border-black/10">
        <div class="pt-2 pb-3 space-y-1 px-2">
            <a href="{{ route('tienda.index') }}" class="block px-3 py-2 rounded-md text-sm font-bold uppercase {{ request()->routeIs('tienda.*') ? 'bg-brick-black text-brick-yellow' : 'text-brick-black' }}">
                {{ __('Tienda') }}
            </a>

            @auth
                <a href="{{ route('carrito.index') }}" class="block px-3 py-2 rounded-md text-sm font-bold uppercase {{ request()->routeIs('carrito.*') ? 'bg-brick-black text-brick-yellow' : 'text-brick-black' }}">
                    {{ __('Carrito') }}
                </a>

                @if(Auth::user()->esAdmin())
                    <a href="{{ route('categorias.index') }}" class="block px-3 py-2 rounded-md text-sm font-bold uppercase {{ request()->routeIs('categorias.*') ? 'bg-brick-black text-brick-yellow' : 'text-brick-black' }}">
                        {{ __('Categorías') }}
                    </a>
                    <a href="{{ route('productos.index') }}" class="block px-3 py-2 rounded-md text-sm font-bold uppercase {{ request()->routeIs('productos.*') ? 'bg-brick-black text-brick-yellow' : 'text-brick-black' }}">
                        {{ __('Productos') }}
                    </a>
                    <a href="{{ route('reportes.index') }}" class="block px-3 py-2 rounded-md text-sm font-bold uppercase {{ request()->routeIs('reportes.*') ? 'bg-brick-black text-brick-yellow' : 'text-brick-black' }}">
                        {{ __('Reportes') }}
                    </a>
                @endif
            @endauth
        </div>

        <div class="pt-4 pb-3 border-t border-black/20 bg-brick-black">
            @auth
                <div class="px-4">
                    <div class="font-bold text-brick-yellow">{{ Auth::user()->name }}</div>
                    <div class="text-sm text-gray-300">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1 px-2">
                    <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-md text-sm text-gray-200 hover:bg-white/10">{{ __('Profile') }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}"
                           onclick="event.preventDefault(); this.closest('form').submit();"
                           class="block px-3 py-2 rounded-md text-sm text-gray-200 hover:bg-white/10 cursor-pointer">
                            {{ __('Log Out') }}
                        </a>
                    </form>
                </div>
            @else
                <div class="space-y-1 px-4">
                    <a href="{{ route('login') }}" class="block py-2 text-sm text-gray-200">{{ __('Iniciar sesión') }}</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="block py-2 text-sm text-gray-200">{{ __('Registrarse') }}</a>
                    @endif
                </div>
            @endauth
        </div>
    </div>
</nav>
