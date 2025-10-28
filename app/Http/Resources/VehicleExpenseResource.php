<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VehicleExpenseResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'vehicle_name' => $this->vehicleName,
            'plate_number' => $this->plateNumber,
            'type' => $this->type,
            'cost' => $this->cost,
            'created_at' => $this->createdAt,
        ];
    }
}
