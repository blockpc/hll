<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'HLL Rosters') }}</title>
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
                <div class="flex items-center gap-3">
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
                </div>

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

        <!-- Hero -->
        <section class="mx-auto grid max-w-7xl gap-12 px-6 py-16 lg:grid-cols-2 lg:px-8 lg:py-24">
            <div class="flex flex-col justify-center">
                <div class="mb-4 inline-flex w-fit items-center gap-2 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-300">
                    Operaciones tácticas para clanes
                </div>

                <h2 class="max-w-3xl text-4xl font-black tracking-tight text-white sm:text-5xl lg:text-6xl">
                    Planifica tu
                    <span class="text-amber-300">clan</span>
                    en Hell Let Loose
                </h2>

                <p class="mt-6 max-w-2xl text-lg leading-8 text-zinc-300">
                    Organiza soldados, crea rosters por mapa y facción, y arma tus escuadras
                    como si prepararas una operación real antes del despliegue.
                </p>

                <div class="mt-8 flex flex-wrap gap-4">
                    @auth
                        <a
                            href="{{ route('dashboard') }}"
                            class="rounded-md border border-amber-500/30 bg-amber-500/15 px-6 py-3 text-sm font-bold uppercase tracking-wider text-amber-200 transition hover:bg-amber-500/25"
                        >
                            Abrir centro de mando
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="rounded-md border border-amber-500/30 bg-amber-500/15 px-6 py-3 text-sm font-bold uppercase tracking-wider text-amber-200 transition hover:bg-amber-500/25"
                        >
                            Iniciar operación
                        </a>

                        @if (!app()->isProduction() && Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="rounded-md border border-white/15 px-6 py-3 text-sm font-bold uppercase tracking-wider text-zinc-200 transition hover:border-white/25 hover:bg-white/5"
                            >
                                Reclutar cuenta
                            </a>
                        @endif
                    @endauth
                </div>

                <dl class="mt-10 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                        <dt class="text-xs font-bold uppercase tracking-[0.2em] text-amber-300">Clanes</dt>
                        <dd class="mt-2 text-sm text-zinc-300">
                            Administra owners, helpers y soldados desde una sola base táctica.
                        </dd>
                    </div>

                    <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                        <dt class="text-xs font-bold uppercase tracking-[0.2em] text-amber-300">Rosters</dt>
                        <dd class="mt-2 text-sm text-zinc-300">
                            Define mapa, facción, punto central y visibilidad pública.
                        </dd>
                    </div>

                    <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                        <dt class="text-xs font-bold uppercase tracking-[0.2em] text-amber-300">Escuadras</dt>
                        <dd class="mt-2 text-sm text-zinc-300">
                            Command, infantry, recon y armor con estructura clara y operativa.
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Preview del producto -->
            <div class="relative">
                <div class="absolute -inset-4 rounded-3xl bg-amber-500/10 blur-3xl"></div>

                <div class="relative overflow-hidden rounded-3xl border border-white/10 bg-zinc-900/80 shadow-2xl shadow-black/40">
                    <div class="flex items-center justify-between border-b border-white/10 bg-black/30 px-5 py-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.25em] text-amber-300">
                                Roster Preview
                            </p>
                            <h3 class="mt-1 text-lg font-bold text-white">
                                Amistoso contra CEL
                            </h3>
                        </div>

                        <div class="rounded-md border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-bold uppercase tracking-wider text-emerald-300">
                            Axis
                        </div>
                    </div>

                    <div class="grid gap-4 p-5 md:grid-cols-2">
                        <div class="rounded-2xl border border-amber-500/20 bg-amber-500/5 p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <h4 class="text-sm font-black uppercase tracking-wider text-amber-200">
                                    Command
                                </h4>
                                <span class="rounded border border-amber-500/30 px-2 py-1 text-[10px] font-bold uppercase text-amber-300">
                                    cmd
                                </span>
                            </div>

                            <div class="space-y-2">
                                <div class="rounded-md border border-white/10 bg-black/20 px-3 py-2 text-sm text-zinc-200">
                                    Nero — Commander
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-sky-500/20 bg-sky-500/5 p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <h4 class="text-sm font-black uppercase tracking-wider text-sky-200">
                                    Infantry 01
                                </h4>
                                <span class="rounded border border-sky-500/30 px-2 py-1 text-[10px] font-bold uppercase text-sky-300">
                                    i01
                                </span>
                            </div>

                            <div class="space-y-2">
                                <div class="rounded-md border border-white/10 bg-black/20 px-3 py-2 text-sm text-zinc-200">
                                    Xunxillo — Squad Leader
                                </div>
                                <div class="rounded-md border border-white/10 bg-black/20 px-3 py-2 text-sm text-zinc-200">
                                    Manolo — Rifleman
                                </div>
                                <div class="rounded-md border border-white/10 bg-black/20 px-3 py-2 text-sm text-zinc-200">
                                    Mendez — Medic
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-violet-500/20 bg-violet-500/5 p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <h4 class="text-sm font-black uppercase tracking-wider text-violet-200">
                                    Recon
                                </h4>
                                <span class="rounded border border-violet-500/30 px-2 py-1 text-[10px] font-bold uppercase text-violet-300">
                                    r01
                                </span>
                            </div>

                            <div class="space-y-2">
                                <div class="rounded-md border border-white/10 bg-black/20 px-3 py-2 text-sm text-zinc-200">
                                    Santosmex — Spotter
                                </div>
                                <div class="rounded-md border border-white/10 bg-black/20 px-3 py-2 text-sm text-zinc-200">
                                    Invitado — Sniper
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-rose-500/20 bg-rose-500/5 p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <h4 class="text-sm font-black uppercase tracking-wider text-rose-200">
                                    Armor
                                </h4>
                                <span class="rounded border border-rose-500/30 px-2 py-1 text-[10px] font-bold uppercase text-rose-300">
                                    a01
                                </span>
                            </div>

                            <div class="space-y-2">
                                <div class="rounded-md border border-white/10 bg-black/20 px-3 py-2 text-sm text-zinc-200">
                                    Driver — Crewman
                                </div>
                                <div class="rounded-md border border-white/10 bg-black/20 px-3 py-2 text-sm text-zinc-200">
                                    Gunner — Crewman
                                </div>
                                <div class="rounded-md border border-white/10 bg-black/20 px-3 py-2 text-sm text-zinc-200">
                                    Tank Lead — Commander
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-white/10 bg-black/20 px-5 py-4 text-xs uppercase tracking-[0.2em] text-zinc-400">
                        Sainte-Marie-du-Mont · Mid Point · Public Roster
                    </div>
                </div>
            </div>
        </section>

        <!-- Características -->
        <section class="mx-auto max-w-7xl px-6 pb-16 lg:px-8 lg:pb-24">
            <div class="mb-8">
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-amber-300">
                    Centro de operaciones
                </p>
                <h3 class="mt-2 text-3xl font-black tracking-tight text-white">
                    Todo lo necesario para planificar antes del combate
                </h3>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                <article class="rounded-2xl border border-white/10 bg-white/5 p-6">
                    <h4 class="text-lg font-bold text-white">Administración de clanes</h4>
                    <p class="mt-3 text-sm leading-6 text-zinc-300">
                        Crea tu clan, gestiona asistentes y mantén un roster limpio de soldados disponibles.
                    </p>
                </article>

                <article class="rounded-2xl border border-white/10 bg-white/5 p-6">
                    <h4 class="text-lg font-bold text-white">Rosters por mapa</h4>
                    <p class="mt-3 text-sm leading-6 text-zinc-300">
                        Prepara configuraciones por facción, mapa y punto central para adaptarte a cada partida.
                    </p>
                </article>

                <article class="rounded-2xl border border-white/10 bg-white/5 p-6">
                    <h4 class="text-lg font-bold text-white">Escuadras organizadas</h4>
                    <p class="mt-3 text-sm leading-6 text-zinc-300">
                        Divide command, infantry, recon y armor con nombres, aliases y miembros bien definidos.
                    </p>
                </article>
            </div>
        </section>

        <!-- CTA final -->
        <section class="border-t border-white/10 bg-black/20">
            <div class="mx-auto max-w-7xl px-6 py-16 text-center lg:px-8">
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-emerald-300">
                    Preparado para desplegar
                </p>

                <h3 class="mt-3 text-3xl font-black tracking-tight text-white sm:text-4xl">
                    Organiza tu próxima operación con disciplina táctica
                </h3>

                <p class="mx-auto mt-4 max-w-2xl text-base leading-7 text-zinc-300">
                    Centraliza clanes, soldados, rosters y escuadras en una sola herramienta diseñada para Hell Let Loose.
                </p>

                <div class="mt-8 flex flex-wrap justify-center gap-4">
                    @auth
                        <a
                            href="{{ route('dashboard') }}"
                            class="rounded-md border border-amber-500/30 bg-amber-500/15 px-6 py-3 text-sm font-bold uppercase tracking-wider text-amber-200 transition hover:bg-amber-500/25"
                        >
                            Ir al panel
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="rounded-md border border-amber-500/30 bg-amber-500/15 px-6 py-3 text-sm font-bold uppercase tracking-wider text-amber-200 transition hover:bg-amber-500/25"
                        >
                            Entrar
                        </a>

                        @if (!app()->isProduction() && Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="rounded-md border border-white/15 px-6 py-3 text-sm font-bold uppercase tracking-wider text-zinc-200 transition hover:border-white/25 hover:bg-white/5"
                            >
                                Crear cuenta
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </section>
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
