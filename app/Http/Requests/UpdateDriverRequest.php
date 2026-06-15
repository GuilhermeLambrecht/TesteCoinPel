<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

// Mesmas regras da criação; só a unicidade do CPF muda (ignora o próprio registro).
class UpdateDriverRequest extends StoreDriverRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['cpf'] = ['required', 'string', 'max:255',
            Rule::unique('drivers', 'cpf')->ignore($this->route('driver'))];

        return $rules;
    }
}
