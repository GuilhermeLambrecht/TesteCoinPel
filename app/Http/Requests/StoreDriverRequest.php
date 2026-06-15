<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDriverRequest extends FormRequest
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
            'cpf' => ['required', 'string', 'max:255', Rule::unique('drivers', 'cpf')],
            'cnh' => ['required', 'string', 'max:255'],
            'cnh_category' => ['required', 'string', 'max:10'],
            'cnh_expiration' => ['required', 'date'],
            'phone' => ['required', 'string', 'max:255'],
            'active' => ['boolean'],
            // Foto opcional; só aceita imagem (tipos seguros) até 2MB.
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'cpf.unique' => 'Já existe um motorista com este CPF.',
        ];
    }
}
