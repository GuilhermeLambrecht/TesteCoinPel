<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    /**
     * Listagem paginada com busca por nome/e-mail/documento.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Client::class);

        $search = trim($request->string('search')->value());

        $clients = Client::search($search)
            ->withCount('contracts')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('clients.index', compact('clients', 'search'));
    }

    public function create(): View
    {
        $this->authorize('create', Client::class);

        return view('clients.create');
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        $this->authorize('create', Client::class);

        Client::create($request->validated());

        return redirect()->route('clients.index')
            ->with('status', 'Cliente criado com sucesso.');
    }

    public function edit(Client $client): View
    {
        $this->authorize('update', $client);

        // Contratos do cliente para exibição (somente leitura) no drawer.
        // with('package') evita N+1 ao mostrar o pacote de cada contrato.
        $contracts = $client->contracts()
            ->with('package')
            ->orderByDesc('id')
            ->get();

        return view('clients.edit', compact('client', 'contracts'));
    }

    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $this->authorize('update', $client);

        $client->update($request->validated());

        return redirect()->route('clients.index')
            ->with('status', 'Cliente atualizado com sucesso.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $this->authorize('delete', $client);

        $client->delete();

        return redirect()->route('clients.index')
            ->with('status', 'Cliente excluído com sucesso.');
    }
}
