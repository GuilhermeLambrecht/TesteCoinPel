@props([
    'title' => '',
    'closeUrl',
])

{{-- Drawer lateral deslizante (slide-over). Renderiza sobre o layout admin;
     fecha no X, no backdrop e no ESC navegando de volta para a listagem. --}}
<div
    x-data="{ shown: false }"
    x-init="requestAnimationFrame(() => shown = true)"
    @keydown.escape.window="window.location.href = @js($closeUrl)"
    class="fixed inset-0 z-40"
    role="dialog"
    aria-modal="true"
    aria-label="{{ $title }}"
>
    {{-- Backdrop --}}
    <a href="{{ $closeUrl }}" aria-label="Fechar"
       class="absolute inset-0 bg-brand-900/40 transition-opacity duration-300"
       :class="shown ? 'opacity-100' : 'opacity-0'"></a>

    {{-- Painel --}}
    <div
        class="absolute inset-y-0 right-0 flex w-full flex-col bg-white shadow-2xl transition-transform duration-300 ease-out sm:max-w-md"
        :class="shown ? 'translate-x-0' : 'translate-x-full'"
    >
        <div class="flex items-center justify-between border-b border-brand-100 px-6 py-4">
            <h2 class="text-lg font-semibold text-brand-900">{{ $title }}</h2>
            <a href="{{ $closeUrl }}" aria-label="Fechar"
               class="rounded-lg p-1.5 text-gray-400 transition hover:bg-brand-50 hover:text-brand-700">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>
        </div>

        {{ $slot }}
    </div>
</div>
