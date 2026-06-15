<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

// Mesmas regras da criação; só a unicidade da placa muda (ignora o próprio registro).
class UpdateVehicleRequest extends StoreVehicleRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['plate'] = ['required', 'string', 'max:255',
            Rule::unique('vehicles', 'plate')->ignore($this->route('vehicle'))];

        return $rules;
    }
}
