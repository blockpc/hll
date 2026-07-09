@props(['title' => null])

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name', 'HLL Rosters') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-zinc-950 text-zinc-100">
    <div class="relative isolate overflow-hidden">
        <!-- Fondo -->
        <div class="absolute inset-0 -z-20 bg-[radial-gradient(circle_at_top,rgba(120,119,60,0.18),transparent_35%),linear-gradient(to_bottom,rgba(24,24,27,0.95),rgba(9,9,11,1))]"></div>

        <!-- Rejilla táctica -->
        <div class="absolute inset-0 -z-10 opacity-10 bg-[linear-gradient(rgba(255,255,255,.08)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,.08)_1px,transparent_1px)] bg-size-[36px_36px]"></div>

        <!-- Header -->
        <header class="border-b border-white/10 bg-black/20 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-md border border-amber-500/40 bg-amber-500/10 text-sm font-bold text-amber-300">
                        {{ config('app.sigla', 'HLL') }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold tracking-[0.2em] text-amber-300 uppercase">
                            {{ config('app.subname', 'Tactical Roster Planner') }}
                        </p>
                        <h1 class="text-base font-bold text-white">
                            {{ config('app.name', 'HLL Rosters') }}
                        </h1>
                    </div>
                </a>

                <div class="flex-1 items-center space-x-3 ml-3">
                    <button
                        type="button"
                        id="mobile-menu-toggle"
                        class="inline-flex items-center justify-center rounded-md border border-white/15 p-2 text-zinc-200 transition hover:border-white/25 hover:bg-white/5 md:hidden"
                        aria-controls="mobile-auth-menu"
                        aria-expanded="false"
                        aria-label="Abrir menú"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M3 5.75A.75.75 0 0 1 3.75 5h12.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 5.75Zm0 4.25a.75.75 0 0 1 .75-.75h12.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 10Zm0 4.25a.75.75 0 0 1 .75-.75h12.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <a @class([
                        "rounded-md border border-white/15 px-4 py-2 text-sm font-semibold text-zinc-200 transition hover:border-white/25 hover:bg-white/5" => !request()->routeIs('public.rosters.index'),
                        "rounded-md border border-amber-500/30 bg-amber-500/10 px-4 py-2 text-sm font-semibold text-amber-200 transition hover:bg-amber-500/20" => request()->routeIs('public.rosters.index'),
                    ])
                        href="{{ route('public.rosters.index') }}"
                    >
                        <span>{{ __('rosters.publics') }}</span>
                    </a>
                </div>

                <nav class="hidden items-center gap-3 md:flex">
                    @auth
                        <a
                            href="{{ route('dashboard') }}"
                            class="rounded-md border border-amber-500/30 bg-amber-500/10 px-4 py-2 text-sm font-semibold text-amber-200 transition hover:bg-amber-500/20"
                        >
                            Ir al panel
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="rounded-md border border-white/15 px-4 py-2 text-sm font-semibold text-zinc-200 transition hover:border-white/25 hover:bg-white/5"
                        >
                            Entrar
                        </a>

                        @if (!app()->isProduction() && Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="rounded-md border border-amber-500/30 bg-amber-500/10 px-4 py-2 text-sm font-semibold text-amber-200 transition hover:bg-amber-500/20"
                            >
                                Crear cuenta
                            </a>
                        @endif
                    @endauth
                </nav>
            </div>

            <div id="mobile-auth-menu" class="hidden border-t border-white/10 bg-black/40 px-6 py-4 md:hidden lg:px-8">
                <nav class="flex flex-col gap-3">
                    <a
                        href="{{ route('public.rosters.index') }}"
                        class="rounded-md border border-amber-500/30 bg-amber-500/10 px-4 py-2 text-sm font-semibold text-amber-200 transition hover:bg-amber-500/20"
                    >
                        <span>{{ __('rosters.publics') }}</span>
                    </a>

                    @auth
                        <a
                            href="{{ route('dashboard') }}"
                            class="rounded-md border border-amber-500/30 bg-amber-500/10 px-4 py-2 text-sm font-semibold text-amber-200 transition hover:bg-amber-500/20"
                        >
                            Ir al panel
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="rounded-md border border-white/15 px-4 py-2 text-sm font-semibold text-zinc-200 transition hover:border-white/25 hover:bg-white/5"
                        >
                            Entrar
                        </a>

                        @if (!app()->isProduction() && Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="rounded-md border border-amber-500/30 bg-amber-500/10 px-4 py-2 text-sm font-semibold text-amber-200 transition hover:bg-amber-500/20"
                            >
                                Crear cuenta
                            </a>
                        @endif
                    @endauth
                </nav>
            </div>
        </header>

        {{ $slot }}
    </div>

    <script>
        (function () {
            const toggleButton = document.getElementById('mobile-menu-toggle');
            const mobileMenu = document.getElementById('mobile-auth-menu');

            if (!toggleButton || !mobileMenu) {
                return;
            }

            const closeMenu = () => {
                mobileMenu.classList.add('hidden');
                toggleButton.setAttribute('aria-expanded', 'false');
            };

            toggleButton.addEventListener('click', () => {
                const isOpen = !mobileMenu.classList.contains('hidden');

                mobileMenu.classList.toggle('hidden');
                toggleButton.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
            });

            mobileMenu.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', closeMenu);
            });

            const mediaQuery = window.matchMedia('(min-width: 768px)');
            mediaQuery.addEventListener('change', (e) => {
                if (e.matches) {
                    closeMenu();
                }
            });
        })();
    </script>
</body>
</html>
