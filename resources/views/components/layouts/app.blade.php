@props([
    'title' => 'Tour Projetos',
    'heading' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} · Tour Projetos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-brand-50 font-sans text-brand-900 antialiased">
    {{-- Checkbox CSS-only para alternar a sidebar no mobile (sem JS). --}}
    <input type="checkbox" id="sidebar-toggle" class="peer hidden">

    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside
            class="fixed inset-y-0 left-0 z-30 flex w-64 -translate-x-full flex-col bg-brand-600 transition-transform
                   peer-checked:translate-x-0 lg:static lg:translate-x-0"
        >
            <div class="flex h-24 items-center justify-center px-6">
                <img src="{{ asset('images/coinPelLogoBranco.png') }}" alt="COINPEL" class="h-16 w-auto object-contain" />
            </div>

            <nav class="flex flex-1 flex-col gap-1 px-3 py-4">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    <x-slot:icon>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </x-slot:icon>
                    Dashboard
                </x-nav-link>

                <x-nav-link :href="route('trips.index')" :active="request()->routeIs('trips.*')">
                    <x-slot:icon>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h8m-8 5h8m-9 8h10a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2zm1-2h.01M15 18h.01" />
                    </x-slot:icon>
                    Viagens
                </x-nav-link>

                <x-nav-link :href="route('vehicles.index')" :active="request()->routeIs('vehicles.*')">
                    <x-slot:icon>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 17H3V6a1 1 0 011-1h11v12m-4 0h2m4 0h2v-5l-3-4h-5" />
                    </x-slot:icon>
                    Veículos
                </x-nav-link>

                <x-nav-link :href="route('drivers.index')" :active="request()->routeIs('drivers.*')">
                    <x-slot:icon>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </x-slot:icon>
                    Motoristas
                </x-nav-link>

                <x-nav-link :href="route('packages.index')" :active="request()->routeIs('packages.*')">
                    <x-slot:icon>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </x-slot:icon>
                    Pacotes
                </x-nav-link>

                <x-nav-link :href="route('contracts.index')" :active="request()->routeIs('contracts.*')">
                    <x-slot:icon>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </x-slot:icon>
                    Contratos
                </x-nav-link>

                <x-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.*')">
                    <x-slot:icon>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 13a2 2 0 100-4 2 2 0 000 4zm0 0c-1.333 0-2.5.667-3 1.5M14 10h4m-4 3h2" />
                    </x-slot:icon>
                    Clientes
                </x-nav-link>

                <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                    <x-slot:icon>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </x-slot:icon>
                    Usuários
                </x-nav-link>

                <x-nav-link :href="route('statistics.index')" :active="request()->routeIs('statistics.*')">
                    <x-slot:icon>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </x-slot:icon>
                    Estatísticas
                </x-nav-link>

                <x-nav-link :href="route('activity-logs.index')" :active="request()->routeIs('activity-logs.*')">
                    <x-slot:icon>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </x-slot:icon>
                    Atividades
                </x-nav-link>
            </nav>
        </aside>

        {{-- Overlay do mobile ao abrir a sidebar --}}
        <label for="sidebar-toggle"
               class="fixed inset-0 z-20 hidden bg-brand-900/40 peer-checked:block lg:hidden"></label>

        {{-- Coluna principal --}}
        <div class="flex min-w-0 flex-1 flex-col">
            {{-- Topbar --}}
            <header class="sticky top-0 z-10 flex h-16 items-center gap-4 border-b border-brand-100 bg-white px-4 sm:px-6">
                <label for="sidebar-toggle"
                       class="cursor-pointer rounded-lg p-2 text-brand-700 hover:bg-brand-50 lg:hidden">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </label>

                <h1 class="text-base font-semibold text-brand-900">
                    {{ $heading ?? $title }}
                </h1>

                {{-- Slot opcional para ações da página (botões, busca, etc.) --}}
                <div class="ml-auto flex items-center gap-3">
                    {{ $actions ?? '' }}

                    <div class="flex items-center gap-2.5">
                        <img src="{{ asset('images/fotoAdmin.png') }}" alt=""
                             class="h-9 w-9 rounded-full object-cover ring-2 ring-brand-100" />
                        <div class="hidden text-sm leading-tight sm:block">
                            <p class="font-medium text-brand-900">{{ auth()->user()?->name ?? 'Administrador' }}</p>
                            <p class="text-xs text-gray-500">Administrador</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="rounded-lg p-2 text-gray-500 transition hover:bg-brand-50 hover:text-brand-700"
                                title="Sair">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </header>

            {{-- Conteúdo --}}
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @if (session('status'))
                    <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ session('error') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
