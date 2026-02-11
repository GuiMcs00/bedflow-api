<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $active = $this->whenLoaded('activeOccupancy');

        return [
            'id' => $this->id,
            'code' => $this->code,
            'status' => $active ? 'occupied' : 'available',
            'patient' => $active
                ? new PatientResource($active->patient)
                : null,
        ];
    }
}
