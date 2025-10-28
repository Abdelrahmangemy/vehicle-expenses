<?php

namespace App\Transformers;

use App\Contracts\ExpenseTransformerInterface;
use App\DTOs\VehicleExpenseDTO;

class ExpenseTransformer implements ExpenseTransformerInterface
{
    public function transform($expense, string $type): VehicleExpenseDTO
    {
        return new VehicleExpenseDTO(
            id: $expense->vehicle->id,
            vehicleName: $expense->vehicle->name,
            plateNumber: $expense->vehicle->plate_number,
            type: $type,
            cost: (float) $expense->cost,
            createdAt: $expense->created_at
        );
    }
}
