<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BedOccupancyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'occupied_at' => optional($this->occupied_at)?->toIso8601String(),
            'released_at' => optional($this->released_at)?->toIso8601String(),

            'bed' => $this->whenLoaded('bed', fn() => [
                'id' => $this->bed->id,
                'code' => $this->bed->code,
            ]),

            'patient' => $this->whenLoaded('patient', fn() => [
                'id' => $this->patient->id,
                'cpf' => $this->patient->cpf,
                'name' => $this->patient->name,
            ]),
        ];
    }
}
