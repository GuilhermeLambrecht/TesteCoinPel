<x-layouts.app title="Editar cliente" heading="Clientes">
    <x-drawer title="Editar cliente" :close-url="route('clients.index')">
        <form method="POST" action="{{ route('clients.update', $client) }}" class="flex min-h-0 flex-1 flex-col">
            @csrf
            @method('PUT')
            <div class="flex-1 overflow-y-auto px-6 py-5">
                @include('clients._form', ['client' => $client])

                {{-- Contratos do cliente (somente leitura) --}}
                <div class="mt-8 border-t border-gray-200 pt-6">
                    <h3 class="text-sm font-semibold text-brand-900">Contratos do cliente</h3>

                    @forelse ($contracts as $contract)
                        <div class="mt-3 flex items-start justify-between gap-3 rounded-lg border border-gray-200 p-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-brand-900">{{ $contract->title }}</p>
                                <p class="mt-0.5 truncate text-xs text-gray-500">
                                    {{ $contract->package?->name }}
                                    <span class="mx-1 text-gray-300">·</span>
                                    {{ $contract->start_date?->format('d/m/Y') }} – {{ $contract->end_date?->format('d/m/Y') }}
                                </p>
                            </div>
                            <x-badge :variant="$contract->status->badgeVariant()">{{ $contract->status->label() }}</x-badge>
                        </div>
                    @empty
                        <p class="mt-3 rounded-lg border border-dashed border-gray-200 p-4 text-center text-sm text-gray-500">
                            Nenhum contrato para este cliente.
                        </p>
                    @endforelse
                </div>
            </div>
            <div class="flex items-center gap-3 border-t border-brand-100 px-6 py-4">
                <x-button type="submit" variant="primary">Salvar alterações</x-button>
                <x-button :href="route('clients.index')" variant="secondary">Cancelar</x-button>
            </div>
        </form>
    </x-drawer>
</x-layouts.app>
