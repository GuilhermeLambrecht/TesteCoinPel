{{-- Admin (foto/nome) + botão Sair (logout). Reutilizado na toolbar das
     listagens e no topo das telas sem toolbar — garante o logout acessível
     em todas as telas, agora que o topbar foi removido. --}}
<div {{ $attributes->merge(['class' => 'flex items-center gap-2.5']) }}>
    <img src="{{ asset('images/fotoAdmin.png') }}" alt=""
         class="h-9 w-9 rounded-full object-cover ring-2 ring-brand-100" />

    <div class="hidden text-sm leading-tight sm:block">
        <p class="font-medium text-brand-900">{{ auth()->user()?->name ?? 'Administrador' }}</p>
        <p class="text-xs text-gray-500">Administrador</p>
    </div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                class="rounded-lg p-2 text-gray-500 transition hover:bg-brand-50 hover:text-brand-700"
                title="Sair" aria-label="Sair">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
        </button>
    </form>
</div>
