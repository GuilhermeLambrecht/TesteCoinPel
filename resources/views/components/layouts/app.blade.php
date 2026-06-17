@props([
    'title' => 'Tour Projetos',
    'heading' => null,
    // true nas telas com toolbar (listagens): elas mesmas renderizam o admin+sair.
    // false (padrão): o layout mostra o admin+sair no topo direito do conteúdo.
    'toolbar' => false,
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
<body class="min-h-screen bg-white font-sans text-brand-900 antialiased">
    {{-- Checkbox CSS-only para alternar a sidebar no mobile (sem JS). --}}
    <input type="checkbox" id="sidebar-toggle" class="peer hidden">

    {{-- Botão flutuante (mobile) para abrir a sidebar — some quando ela está aberta.
         Substitui o antigo hambúrguer do topbar, que foi removido. --}}
    <label for="sidebar-toggle"
           class="fixed left-3 top-3 z-40 cursor-pointer rounded-lg bg-white p-2 text-brand-700 shadow-sm ring-1 ring-brand-100 lg:hidden peer-checked:hidden">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </label>

    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside
            class="fixed inset-y-0 left-0 z-30 flex w-56 -translate-x-full flex-col bg-brand-600 transition-transform
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

                <x-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.*')"
                            :icon-image="asset('images/system-uicons_users.png')">
                    Clientes
                </x-nav-link>

                <x-nav-link :href="route('drivers.index')" :active="request()->routeIs('drivers.*')"
                            :icon-image="asset('images/'.rawurlencode('system-uicons_users (1).png'))">
                    Motoristas
                </x-nav-link>

                <x-nav-link :href="route('statistics.index')" :active="request()->routeIs('statistics.*')"
                            :icon-image="asset('images/system-uicons_graph-bar.png')">
                    Estatísticas
                </x-nav-link>

                <x-nav-link :href="route('vehicles.index')" :active="request()->routeIs('vehicles.*')"
                            :icon-image="asset('images/carbon_bus.png')">
                    Veículos
                </x-nav-link>

                <x-nav-link :href="route('trips.index')" :active="request()->routeIs('trips.*')"
                            :icon-image="asset('images/system-uicons_document.png')">
                    Viagens
                </x-nav-link>

                <x-nav-link :href="route('contracts.index')" :active="request()->routeIs('contracts.*')"
                            :icon-image="asset('images/teenyicons_contract-outline.png')">
                    Contratos
                </x-nav-link>

                <x-nav-link :href="route('packages.index')" :active="request()->routeIs('packages.*')"
                            :icon-image="asset('images/ion_wallet-outline.svg')">
                    Pacotes
                </x-nav-link>

                <x-nav-link :href="route('activity-logs.index')" :active="request()->routeIs('activity-logs.*')">
                    <x-slot:icon>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </x-slot:icon>
                    Atividades
                </x-nav-link>

                <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                    <x-slot:icon>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </x-slot:icon>
                    Usuários
                </x-nav-link>
            </nav>
        </aside>

        {{-- Overlay do mobile ao abrir a sidebar --}}
        <label for="sidebar-toggle"
               class="fixed inset-0 z-20 hidden bg-brand-900/40 peer-checked:block lg:hidden"></label>

        {{-- Coluna principal (sem topbar; o fundo é branco) --}}
        <div class="flex min-w-0 flex-1 flex-col">
            {{-- Padding horizontal dá respiro ao conteúdo; o pt maior no mobile
                 abre espaço para o botão flutuante da sidebar. As tabelas "flush"
                 cancelam esse padding lateral para encostar nas bordas. --}}
            <main class="flex-1 px-4 pb-8 pt-16 sm:px-6 lg:px-8 lg:py-8">
                {{-- Telas SEM toolbar: admin + sair alinhados à direita no topo,
                     garantindo o logout acessível (Dashboard, Estatísticas, forms…). --}}
                @unless ($toolbar)
                    <div class="mb-6 flex justify-end">
                        <x-user-menu />
                    </div>
                @endunless

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
