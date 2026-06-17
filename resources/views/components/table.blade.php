@props([
    'headers' => [],
    // flush: ocupa 100% da largura encostando nas bordas (cancela o padding
    // lateral do <main>). Usado nas listagens.
    'flush' => false,
])

{{-- Tabela "clean": fundo branco (igual à página), separada apenas por fios
     claros (sem zebra/fundo alternado). Cabeçalho discreto e texto suave.
     O padding das células preserva o respiro interno; a rolagem horizontal
     mantém a responsividade. --}}
<div @class([
    'overflow-x-auto bg-white',
    '-mx-4 sm:-mx-6 lg:-mx-8' => $flush,
    'rounded-xl' => ! $flush,
])>
    <table class="min-w-full text-sm">
        @if (count($headers))
            <thead>
                <tr class="border-b border-gray-200">
                    @foreach ($headers as $header)
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-400 whitespace-nowrap">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
        @endif

        <tbody class="divide-y divide-gray-100 text-gray-600">
            {{ $slot }}
        </tbody>
    </table>
</div>
