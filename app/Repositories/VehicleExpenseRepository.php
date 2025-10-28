<?php

namespace App\Repositories;

use App\Contracts\ExpenseRepositoryInterface;
use App\DTOs\ExpenseFilterDTO;
use App\Models\FuelEntry;
use App\Models\InsurancePayment;
use App\Models\Service;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class VehicleExpenseRepository implements ExpenseRepositoryInterface
{
    public function getExpenses(ExpenseFilterDTO $filters): Collection
    {
        $expenses = collect();

        if ($this->shouldIncludeType('fuel', $filters->types)) {
            $fuelExpenses = $this->getFuelEntries($filters);
            $expenses = $expenses->merge($fuelExpenses);
        }

        if ($this->shouldIncludeType('insurance', $filters->types)) {
            $insuranceExpenses = $this->getInsurancePayments($filters);
            $expenses = $expenses->merge($insuranceExpenses);
        }

        if ($this->shouldIncludeType('service', $filters->types)) {
            $serviceExpenses = $this->getServices($filters);
            $expenses = $expenses->merge($serviceExpenses);
        }

        return $expenses;
    }

    private function getFuelEntries(ExpenseFilterDTO $filters): Collection
    {
        $query = FuelEntry::with('vehicle')
            ->select(
                'fuel_entries.id',
                'fuel_entries.vehicle_id',
                'fuel_entries.cost',
                'fuel_entries.entry_date as created_at',
                DB::raw("'fuel' as type")
            )
            ->join('vehicles', 'fuel_entries.vehicle_id', '=', 'vehicles.id');

        $this->applyFilters($query, $filters, 'cost', 'entry_date');

        return $query->get();
    }

    private function getInsurancePayments(ExpenseFilterDTO $filters): Collection
    {
        $query = InsurancePayment::with('vehicle')
            ->select(
                'insurance_payments.id',
                'insurance_payments.vehicle_id',
                'insurance_payments.amount as cost',
                'insurance_payments.contract_date as created_at',
                DB::raw("'insurance' as type")
            )
            ->join('vehicles', 'insurance_payments.vehicle_id', '=', 'vehicles.id');

        $this->applyFilters($query, $filters, 'amount', 'contract_date');

        return $query->get();
    }

    private function getServices(ExpenseFilterDTO $filters): Collection
    {
        $query = Service::with('vehicle')
            ->select(
                'services.id',
                'services.vehicle_id',
                'services.total as cost',
                'services.created_at',
                DB::raw("'service' as type")
            )
            ->join('vehicles', 'services.vehicle_id', '=', 'vehicles.id');

        $this->applyFilters($query, $filters, 'total', 'services.created_at');

        return $query->get();
    }

    private function applyFilters($query, ExpenseFilterDTO $filters, string $costColumn, string $dateColumn): void
    {
        if ($filters->search) {
            $query->where('vehicles.name', 'LIKE', "%{$filters->search}%");
        }

        if ($filters->minCost !== null) {
            $query->where($costColumn, '>=', $filters->minCost);
        }

        if ($filters->maxCost !== null) {
            $query->where($costColumn, '<=', $filters->maxCost);
        }

        if ($filters->minDate) {
            $query->where($dateColumn, '>=', $filters->minDate);
        }

        if ($filters->maxDate) {
            $query->where($dateColumn, '<=', $filters->maxDate);
        }
    }

    private function shouldIncludeType(string $type, ?array $types): bool
    {
        return $types === null || in_array($type, $types);
    }
}
