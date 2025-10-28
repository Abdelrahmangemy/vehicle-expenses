<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleExpenseRequest;
use App\Http\Resources\VehicleExpenseCollection;
use App\Services\VehicleExpenseService;
use App\DTOs\ExpenseFilterDTO;

class VehicleExpenseController extends Controller
{
    public function __construct(
        private VehicleExpenseService $expenseService
    ) {}

    public function index(VehicleExpenseRequest $request)
    {
        $filters = ExpenseFilterDTO::fromRequest($request);

        $expenses = $this->expenseService->getExpenses($filters);

        return new VehicleExpenseCollection($expenses);
    }
}
