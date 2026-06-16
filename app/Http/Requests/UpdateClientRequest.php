<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

// Mesmas regras da criação; só a unicidade de e-mail/documento ignora o próprio registro.
class UpdateClientRequest extends StoreClientRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        $client = $this->route('client');

        $rules['email'] = ['required', 'email', 'max:255',
            Rule::unique('clients', 'email')->ignore($client)];
        $rules['document'] = ['required', 'string', 'max:255',
            Rule::unique('clients', 'document')->ignore($client)];

        return $rules;
    }
}
