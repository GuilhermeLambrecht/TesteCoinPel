<x-layouts.app title="Atividades" heading="Atividades">
    <div class="space-y-4">
        <p class="text-sm text-gray-500">Registro das ações de criação, edição e exclusão (somente leitura).</p>

        <x-table :headers="['Data/Hora', 'Usuário', 'Ação', 'Registro']">
            @forelse ($logs as $log)
                @php
                    $variant = match ($log->action) {
                        'created' => 'success',
                        'updated' => 'info',
                        'deleted' => 'danger',
                        default => 'neutral',
                    };
                    $actionLabel = match ($log->action) {
                        'created' => 'Criou',
                        'updated' => 'Editou',
                        'deleted' => 'Excluiu',
                        default => $log->action,
                    };
                    $subject = match (class_basename($log->subject_type)) {
                        'Trip' => 'Viagem',
                        'Vehicle' => 'Veículo',
                        'Driver' => 'Motorista',
                        'User' => 'Usuário',
                        default => class_basename($log->subject_type),
                    };
                @endphp
                <tr class="transition hover:bg-gray-50">
                    <td class="px-4 py-3 whitespace-nowrap">{{ $log->created_at?->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3">{{ $log->user?->name ?? '—' }}</td>
                    <td class="px-4 py-3"><x-badge :variant="$variant">{{ $actionLabel }}</x-badge></td>
                    <td class="px-4 py-3">
                        <span class="font-medium text-brand-900">{{ $subject }}</span>
                        @if ($log->description)
                            <span class="text-gray-500">— {{ $log->description }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-10 text-center text-gray-500">
                        Nenhuma atividade registrada.
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div>
            {{ $logs->links() }}
        </div>
    </div>
</x-layouts.app>
