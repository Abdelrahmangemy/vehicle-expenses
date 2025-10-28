<?php

namespace App\Contracts;

use App\DTOs\ExpenseFilterDTO;
use Illuminate\Support\Collection;

interface ExpenseRepositoryInterface
{
    public function getExpenses(ExpenseFilterDTO $filters): Collection;
}
