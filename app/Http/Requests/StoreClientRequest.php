<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientRequest extends FormRequest
{
    // A autorização é centralizada no controller (via Policy).

    /**
     * Normaliza o checkbox `active` para booleano antes de validar.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'active' => $this->boolean('active'),
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('clients', 'email')],
            'phone' => ['required', 'string', 'max:255'],
            'document' => ['required', 'string', 'max:255', Rule::unique('clients', 'document')],
            'active' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'Já existe um cliente com este e-mail.',
            'document.unique' => 'Já existe um cliente com este documento.',
        ];
    }
}
