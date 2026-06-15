<?php

namespace App\Http\Requests;

use App\Models\Trip;

class UpdateTripRequest extends StoreTripRequest
{
    /**
     * No update, o veículo/motorista já vinculado é aceito mesmo inativo (não
     * força a troca); ainda assim não se pode escolher um inativo diferente.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        /** @var Trip $trip */
        $trip = $this->route('trip');

        $rules['vehicle_id'] = ['required', $this->activeExistsRule('vehicles', $trip->vehicle_id)];
        $rules['driver_id'] = ['required', $this->activeExistsRule('drivers', $trip->driver_id)];

        return $rules;
    }

    /**
     * A checagem de conflito ignora a própria viagem que está sendo editada.
     */
    protected function ignoredTripId(): ?int
    {
        /** @var Trip $trip */
        $trip = $this->route('trip');

        return $trip->id;
    }
}
