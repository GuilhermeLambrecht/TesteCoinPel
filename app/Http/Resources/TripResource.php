<?php

namespace App\Http\Resources;

use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Trip
 */
class TripResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'origin' => $this->origin,
            'destination' => $this->destination,
            // Carbon (cast datetime) serializa em ISO8601 automaticamente no JSON.
            'departure_at' => $this->departure_at,
            'arrival_at' => $this->arrival_at,
            // Enum (backed) serializa para o seu valor string no JSON.
            'status' => $this->status,
            'vehicle' => VehicleResource::make($this->whenLoaded('vehicle')),
            'driver' => DriverResource::make($this->whenLoaded('driver')),
        ];
    }
}
