<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVehicleRequest extends FormRequest
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
            'plate' => ['required', 'string', 'max:255', Rule::unique('vehicles', 'plate')],
            'model' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'year' => ['required', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'active' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'plate.unique' => 'Já existe um veículo com esta placa.',
        ];
    }
}
