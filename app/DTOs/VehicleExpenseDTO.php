<?php

namespace App\DTOs;

class VehicleExpenseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $vehicleName,
        public readonly string $plateNumber,
        public readonly string $type,
        public readonly float $cost,
        public readonly string $createdAt
    ) {}

    public function toArray(): array
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
