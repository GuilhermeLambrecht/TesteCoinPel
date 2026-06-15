@props([
    'title' => 'Tour Projetos',
])

{{-- Layout dividido das telas de autenticação: metade branca (conteúdo
     centralizado e contido) + metade roxa com a ilustração ancorada na base
     (marca d'água). Em telas pequenas o painel roxo some e o conteúdo ocupa a
     largura total, centralizado.

     Slot opcional `modal`: renderiza um card sobreposto (não dispensável) sobre
     o layout dividido — usado pela troca de senha obrigatória do 1º acesso. --}}
<x-layouts.guest :title="$title">
    <div class="flex min-h-screen">
        {{-- Painel branco (50%) --}}
        <div class="flex w-full min-w-0 items-center justify-center px-6 py-12 sm:px-10 lg:w-1/2">
            <div class="w-full max-w-sm">
                {{ $slot }}
            </div>
        </div>

        {{-- Painel roxo (50%) com a ilustração ancorada embaixo --}}
        <div class="relative hidden min-w-0 overflow-hidden bg-brand-600 lg:block lg:w-1/2">
            <x-auth-watermark />
        </div>
    </div>

    @isset($modal)
        {{-- Modal sobreposto sobre o painel dividido. Sem botão de fechar e sem
             dispensar por backdrop/ESC: o 1º acesso (RF06) só avança enviando a
             nova senha; o middleware ForcePasswordChange mantém a obrigatoriedade. --}}
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-brand-900/50 p-4">
            {{ $modal }}
        </div>
    @endisset
</x-layouts.guest>
