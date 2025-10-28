<?php

namespace App\Services;

use App\Contracts\ExpenseRepositoryInterface;
use App\Contracts\ExpenseTransformerInterface;
use App\DTOs\ExpenseFilterDTO;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class VehicleExpenseService
{
    public function __construct(
        private ExpenseRepositoryInterface $repository,
        private ExpenseTransformerInterface $transformer
    ) {}

    public function getExpenses(ExpenseFilterDTO $filters): LengthAwarePaginator
    {
        $expenses = $this->repository->getExpenses($filters);

        $transformed = $expenses->map(function ($expense) {
            return $this->transformer->transform($expense, $expense->type);
        });

        $sorted = $this->applySorting($transformed, $filters);

        return $this->paginateCollection($sorted, $filters->perPage, $filters->page);
    }

    private function applySorting(Collection $expenses, ExpenseFilterDTO $filters): Collection
    {
        $sortBy = $filters->sortBy === 'cost' ? 'cost' : 'createdAt';
        $direction = strtolower($filters->sortDirection) === 'asc' ? 'sortBy' : 'sortByDesc';

        return $expenses->$direction($sortBy)->values();
    }

    private function paginateCollection(Collection $collection, int $perPage, int $page): LengthAwarePaginator
    {
        $items = $collection->forPage($page, $perPage);
        return new LengthAwarePaginator(
            items: $items,
            total: $collection->count(),
            perPage: $perPage,
            currentPage: $page,
        );
    }
}
