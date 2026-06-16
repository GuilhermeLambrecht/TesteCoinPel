<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePackageRequest;
use App\Http\Requests\UpdatePackageRequest;
use App\Models\Package;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PackageController extends Controller
{
    /**
     * Listagem paginada com busca por nome/destino.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Package::class);

        $search = trim($request->string('search')->value());

        $packages = Package::search($search)
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('packages.index', compact('packages', 'search'));
    }

    public function create(): View
    {
        $this->authorize('create', Package::class);

        return view('packages.create');
    }

    public function store(StorePackageRequest $request): RedirectResponse
    {
        $this->authorize('create', Package::class);

        Package::create($request->validated());

        return redirect()->route('packages.index')
            ->with('status', 'Pacote criado com sucesso.');
    }

    public function edit(Package $package): View
    {
        $this->authorize('update', $package);

        return view('packages.edit', compact('package'));
    }

    public function update(UpdatePackageRequest $request, Package $package): RedirectResponse
    {
        $this->authorize('update', $package);

        $package->update($request->validated());

        return redirect()->route('packages.index')
            ->with('status', 'Pacote atualizado com sucesso.');
    }

    public function destroy(Package $package): RedirectResponse
    {
        $this->authorize('delete', $package);

        $package->delete();

        return redirect()->route('packages.index')
            ->with('status', 'Pacote excluído com sucesso.');
    }
}
