<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDriverRequest;
use App\Http\Requests\UpdateDriverRequest;
use App\Models\Driver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DriverController extends Controller
{
    /**
     * Listagem paginada com busca por nome/CPF.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Driver::class);

        $search = trim($request->string('search')->value());

        $drivers = Driver::search($search)
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('drivers.index', compact('drivers', 'search'));
    }

    public function create(): View
    {
        $this->authorize('create', Driver::class);

        return view('drivers.create');
    }

    public function store(StoreDriverRequest $request): RedirectResponse
    {
        $this->authorize('create', Driver::class);

        $data = $request->safe()->except('photo');

        $photo = $request->file('photo');
        if ($photo instanceof UploadedFile) {
            // store() gera um nome único; o nome original enviado nunca é usado.
            $data['photo_path'] = $photo->store('drivers', 'public');
        }

        Driver::create($data);

        return redirect()->route('drivers.index')
            ->with('status', 'Motorista criado com sucesso.');
    }

    public function edit(Driver $driver): View
    {
        $this->authorize('update', $driver);

        return view('drivers.edit', compact('driver'));
    }

    public function update(UpdateDriverRequest $request, Driver $driver): RedirectResponse
    {
        $this->authorize('update', $driver);

        $data = $request->safe()->except('photo');

        $photo = $request->file('photo');
        if ($photo instanceof UploadedFile) {
            $newPath = $photo->store('drivers', 'public');

            // Ao trocar a foto, remove a antiga do storage.
            $old = $driver->photo_path;
            if (is_string($old) && $old !== '') {
                Storage::disk('public')->delete($old);
            }

            $data['photo_path'] = $newPath;
        }

        $driver->update($data);

        return redirect()->route('drivers.index')
            ->with('status', 'Motorista atualizado com sucesso.');
    }

    public function destroy(Driver $driver): RedirectResponse
    {
        $this->authorize('delete', $driver);

        $driver->delete();

        return redirect()->route('drivers.index')
            ->with('status', 'Motorista excluído com sucesso.');
    }
}
