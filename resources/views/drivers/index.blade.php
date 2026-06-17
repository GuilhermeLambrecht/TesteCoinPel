<x-layouts.app title="Motoristas" :toolbar="true">
    <div class="space-y-6">
        {{-- Barra superior no novo padrão; o conteúdo segue em cards (abaixo). --}}
        <x-list-toolbar
            :create-url="route('drivers.create')"
            create-label="+ Adicionar motorista"
            :action="route('drivers.index')"
            :search="$search"
            placeholder="Pesquisar por nome ou CPF"
        />

        {{-- Listagem em cards: 1 por linha no mobile, 2 no desktop --}}
        @if ($drivers->count())
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($drivers as $driver)
                    <div class="flex items-start gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:bg-gray-50">
                        <x-avatar :src="$driver->photo_url" :name="$driver->name" size="h-14 w-14" class="shrink-0" />

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <p class="truncate font-medium text-brand-900">{{ $driver->name }}</p>
                                <x-badge :variant="$driver->active ? 'success' : 'neutral'">
                                    {{ $driver->active ? 'Ativo' : 'Inativo' }}
                                </x-badge>
                            </div>
                            <p class="mt-0.5 truncate text-sm text-gray-500">CPF {{ $driver->cpf }} · {{ $driver->phone }}</p>
                            <p class="truncate text-xs text-gray-400">CNH {{ $driver->cnh }} · Cat. {{ $driver->cnh_category }}</p>
                        </div>

                        <div class="shrink-0">
                            <x-row-actions
                                :edit-url="route('drivers.edit', $driver)"
                                :delete-url="route('drivers.destroy', $driver)"
                                confirm="Excluir o motorista {{ $driver->name }}?"
                            />
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-xl border border-gray-200 bg-white p-10 text-center text-gray-500 shadow-sm">
                Nenhum motorista encontrado.
            </div>
        @endif

        <div>
            {{ $drivers->links() }}
        </div>
    </div>
</x-layouts.app>
