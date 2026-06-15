<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PasswordChangeController extends Controller
{
    /**
     * Exibe a tela de troca de senha do primeiro acesso.
     */
    public function show(): View
    {
        return view('auth.change-password');
    }

    /**
     * Salva a nova senha e libera o acesso ao sistema (RF06).
     */
    public function update(ChangePasswordRequest $request): RedirectResponse
    {
        // O cast `hashed` do model aplica o hash automaticamente.
        $request->user()->update([
            'password' => $request->validated('password'),
            'must_change_password' => false,
        ]);

        return redirect()->route('dashboard');
    }
}
