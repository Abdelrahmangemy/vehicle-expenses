<?php

namespace App\Contracts;

use App\DTOs\VehicleExpenseDTO;

interface ExpenseTransformerInterface
{
    public function transform($expense, string $type): VehicleExpenseDTO;
}
