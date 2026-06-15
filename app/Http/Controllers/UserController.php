<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Listagem paginada com busca por nome/e-mail.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $search = trim($request->string('search')->value());

        $users = User::search($search)
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('users.index', compact('users', 'search'));
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $data = $request->validated();

        // Novo usuário nasce obrigado a trocar a senha no 1º acesso (RF06).
        // O cast `hashed` do model aplica o hash na senha.
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'must_change_password' => true,
        ]);

        return redirect()->route('users.index')
            ->with('status', 'Usuário criado com sucesso.');
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $data = $request->validated();

        $user->name = $data['name'];
        $user->email = $data['email'];

        // Senha opcional: só regrava (com hash) quando informada.
        if (filled($data['password'] ?? null)) {
            $user->password = $data['password'];
        }

        $user->save();

        return redirect()->route('users.index')
            ->with('status', 'Usuário atualizado com sucesso.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        // Proteção contra lockout: ninguém exclui a própria conta.
        if ($user->is($request->user())) {
            return redirect()->route('users.index')
                ->with('error', 'Você não pode excluir a própria conta.');
        }

        $this->authorize('delete', $user);

        $user->delete();

        return redirect()->route('users.index')
            ->with('status', 'Usuário excluído com sucesso.');
    }
}
