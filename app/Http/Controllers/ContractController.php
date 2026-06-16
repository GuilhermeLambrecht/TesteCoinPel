<?php

namespace App\Http\Controllers;

use App\Enums\ContractStatus;
use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\UpdateContractRequest;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Package;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContractController extends Controller
{
    /**
     * Listagem paginada com eager loading do pacote (sem N+1) e busca por título.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Contract::class);

        $search = trim($request->string('search')->value());

        $contracts = Contract::with(['client', 'package'])
            ->search($search)
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('contracts.index', compact('contracts', 'search'));
    }

    public function create(): View
    {
        $this->authorize('create', Contract::class);

        return view('contracts.create', $this->formOptions());
    }

    public function store(StoreContractRequest $request): RedirectResponse
    {
        $this->authorize('create', Contract::class);

        Contract::create($request->validated());

        return redirect()->route('contracts.index')
            ->with('status', 'Contrato criado com sucesso.');
    }

    public function edit(Contract $contract): View
    {
        $this->authorize('update', $contract);

        return view('contracts.edit', [...$this->formOptions($contract), 'contract' => $contract]);
    }

    public function update(UpdateContractRequest $request, Contract $contract): RedirectResponse
    {
        $this->authorize('update', $contract);

        $contract->update($request->validated());

        return redirect()->route('contracts.index')
            ->with('status', 'Contrato atualizado com sucesso.');
    }

    public function destroy(Contract $contract): RedirectResponse
    {
        $this->authorize('delete', $contract);

        $contract->delete();

        return redirect()->route('contracts.index')
            ->with('status', 'Contrato excluído com sucesso.');
    }

    /**
     * Opções do formulário: clientes e pacotes ativos. Na edição (quando há
     * contrato), inclui também o cliente/pacote já vinculado, mesmo inativo,
     * marcado como "(inativo)".
     *
     * @return array{clients: array<int, string>, packages: array<int, string>, statuses: array<string, string>}
     */
    private function formOptions(?Contract $contract = null): array
    {
        return [
            'clients' => $this->clientOptions($contract?->client_id),
            'packages' => $this->packageOptions($contract?->package_id),
            'statuses' => ContractStatus::options(),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function clientOptions(?int $currentId): array
    {
        return Client::query()
            ->where(function ($query) use ($currentId) {
                // Ativos OU o cliente já vinculado (mesmo inativo).
                $query->where('active', true);
                if ($currentId !== null) {
                    $query->orWhere('id', $currentId);
                }
            })
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (Client $client) => [
                $client->id => $client->name.($client->active ? '' : ' (inativo)'),
            ])
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function packageOptions(?int $currentId): array
    {
        return Package::query()
            ->where(function ($query) use ($currentId) {
                // Ativos OU o pacote já vinculado (mesmo inativo).
                $query->where('active', true);
                if ($currentId !== null) {
                    $query->orWhere('id', $currentId);
                }
            })
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (Package $package) => [
                $package->id => $package->name.' — '.$package->destination.($package->active ? '' : ' (inativo)'),
            ])
            ->all();
    }
}
