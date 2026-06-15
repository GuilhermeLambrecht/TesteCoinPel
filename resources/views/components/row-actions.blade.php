@props([
    'editUrl',
    'deleteUrl',
    'confirm' => 'Tem certeza que deseja excluir este registro?',
    'showDelete' => true,
])

{{-- Menu de ações "..." por linha. O painel usa posição `fixed` (posicionado via
     Alpine) para não ser cortado pelo overflow da tabela. --}}
<div
    x-data="{
        open: false,
        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.$nextTick(() => {
                    const r = this.$refs.trigger.getBoundingClientRect();
                    const menu = this.$refs.menu;
                    menu.style.top = (r.bottom + 4) + 'px';
                    menu.style.left = (r.right - menu.offsetWidth) + 'px';
                });
            }
        },
    }"
    @keydown.escape.window="open = false"
    @scroll.window="open = false"
    @resize.window="open = false"
    class="flex justify-end"
>
    <button
        type="button"
        x-ref="trigger"
        @click="toggle()"
        :aria-expanded="open"
        aria-label="Ações"
        class="rounded-lg p-1.5 text-gray-400 transition hover:bg-brand-50 hover:text-brand-700"
    >
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
            <circle cx="5" cy="12" r="1.5" />
            <circle cx="12" cy="12" r="1.5" />
            <circle cx="19" cy="12" r="1.5" />
        </svg>
    </button>

    <div
        x-ref="menu"
        x-show="open"
        x-cloak
        x-transition.opacity.duration.150ms
        @click.outside="open = false"
        class="fixed z-50 w-44 overflow-hidden rounded-lg border border-brand-100 bg-white py-1 shadow-xl"
    >
        <a href="{{ $editUrl }}"
           class="flex items-center gap-2.5 px-4 py-2 text-sm text-brand-900 transition hover:bg-brand-50">
            <svg class="h-4 w-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Editar
        </a>

        @if ($showDelete)
            <form method="POST" action="{{ $deleteUrl }}" onsubmit="return confirm(@js($confirm))">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="flex w-full items-center gap-2.5 px-4 py-2 text-left text-sm text-red-600 transition hover:bg-red-50">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Excluir
                </button>
            </form>
        @endif
    </div>
</div>
