<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateUserRequest extends StoreUserRequest
{
    /**
     * Em relação à criação: e-mail único ignora o próprio registro e a senha
     * passa a ser opcional (vazia mantém a atual).
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['email'] = ['required', 'string', 'email', 'max:255',
            Rule::unique('users', 'email')->ignore($this->route('user'))];
        $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];

        return $rules;
    }
}
