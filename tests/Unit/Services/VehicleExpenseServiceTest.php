<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\VehicleExpenseService;
use App\Contracts\ExpenseRepositoryInterface;
use App\Contracts\ExpenseTransformerInterface;
use App\DTOs\ExpenseFilterDTO;
use App\DTOs\VehicleExpenseDTO;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;

class VehicleExpenseServiceTest extends TestCase
{
    private $repository;
    private $transformer;
    private $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(ExpenseRepositoryInterface::class);
        $this->transformer = Mockery::mock(ExpenseTransformerInterface::class);
        $this->service = new VehicleExpenseService($this->repository, $this->transformer);
    }

    public function test_get_expenses_returns_paginated_collection()
    {
        $filters = new ExpenseFilterDTO();

        $mockExpense = $this->createMockExpense('fuel', 100, '2025-01-01');
        $mockDTO = $this->createMockDTO(1, 'Toyota', 'ABC-123', 'fuel', 100, '2025-01-01');

        $this->repository
            ->shouldReceive('getExpenses')
            ->with($filters)
            ->once()
            ->andReturn(collect([$mockExpense]));

        $this->transformer
            ->shouldReceive('transform')
            ->with($mockExpense, 'fuel')
            ->once()
            ->andReturn($mockDTO);

        $result = $this->service->getExpenses($filters);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(1, $result->total());
        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(1, $result->currentPage());
    }

    public function test_get_expenses_handles_empty_result()
    {
        $filters = new ExpenseFilterDTO();

        $this->repository
            ->shouldReceive('getExpenses')
            ->with($filters)
            ->once()
            ->andReturn(collect([]));

        $result = $this->service->getExpenses($filters);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(0, $result->total());
        $this->assertEmpty($result->items());
    }

    public function test_expenses_sorted_by_cost_ascending()
    {
        $filters = new ExpenseFilterDTO(sortBy: 'cost', sortDirection: 'asc');

        $expense1 = $this->createMockExpense('fuel', 300, '2025-01-01');
        $expense2 = $this->createMockExpense('fuel', 100, '2025-01-02');
        $expense3 = $this->createMockExpense('fuel', 200, '2025-01-03');

        $dto1 = $this->createMockDTO(1, 'Car1', 'ABC', 'fuel', 300, '2025-01-01');
        $dto2 = $this->createMockDTO(2, 'Car2', 'DEF', 'fuel', 100, '2025-01-02');
        $dto3 = $this->createMockDTO(3, 'Car3', 'GHI', 'fuel', 200, '2025-01-03');

        $this->repository
            ->shouldReceive('getExpenses')
            ->andReturn(collect([$expense1, $expense2, $expense3]));

        $this->transformer
            ->shouldReceive('transform')
            ->andReturn($dto1, $dto2, $dto3);

        $result = $this->service->getExpenses($filters);
        $items = collect($result->items());

        $this->assertEquals(100, $items[0]->cost);
        $this->assertEquals(200, $items[1]->cost);
        $this->assertEquals(300, $items[2]->cost);
    }

    public function test_expenses_sorted_by_date_ascending()
    {
        $filters = new ExpenseFilterDTO(sortBy: 'created_at', sortDirection: 'asc');

        $expense1 = $this->createMockExpense('fuel', 100, '2025-01-03');
        $expense2 = $this->createMockExpense('fuel', 200, '2025-01-01');
        $expense3 = $this->createMockExpense('fuel', 300, '2025-01-02');

        $dto1 = $this->createMockDTO(1, 'Car1', 'ABC', 'fuel', 100, '2025-01-03');
        $dto2 = $this->createMockDTO(2, 'Car2', 'DEF', 'fuel', 200, '2025-01-01');
        $dto3 = $this->createMockDTO(3, 'Car3', 'GHI', 'fuel', 300, '2025-01-02');

        $this->repository
            ->shouldReceive('getExpenses')
            ->andReturn(collect([$expense1, $expense2, $expense3]));

        $this->transformer
            ->shouldReceive('transform')
            ->andReturn($dto1, $dto2, $dto3);

        $result = $this->service->getExpenses($filters);
        $items = collect($result->items());

        $this->assertEquals('2025-01-01', $items[0]->createdAt);
        $this->assertEquals('2025-01-02', $items[1]->createdAt);
        $this->assertEquals('2025-01-03', $items[2]->createdAt);
    }

    public function test_handles_single_expense()
    {
        $filters = new ExpenseFilterDTO();

        $expense = $this->createMockExpense('fuel', 100, '2025-01-01');
        $dto = $this->createMockDTO(1, 'Toyota', 'ABC', 'fuel', 100, '2025-01-01');

        $this->repository
            ->shouldReceive('getExpenses')
            ->andReturn(collect([$expense]));

        $this->transformer
            ->shouldReceive('transform')
            ->andReturn($dto);

        $result = $this->service->getExpenses($filters);

        $this->assertEquals(1, $result->total());
        $this->assertCount(1, $result->items());
    }

    public function test_handles_large_dataset()
    {
        $filters = new ExpenseFilterDTO(perPage: 50, page: 10);

        $expenses = collect();
        for ($i = 1; $i <= 1000; $i++) {
            $expenses->push($this->createMockExpense('fuel', $i, '2025-01-01'));
        }

        $this->repository
            ->shouldReceive('getExpenses')
            ->andReturn($expenses);

        $this->transformer
            ->shouldReceive('transform')
            ->andReturn($this->createMockDTO(1, 'Car', 'ABC', 'fuel', 100, '2025-01-01'));

        $result = $this->service->getExpenses($filters);

        $this->assertEquals(1000, $result->total());
        $this->assertEquals(50, $result->perPage());
        $this->assertEquals(10, $result->currentPage());
        $this->assertEquals(20, $result->lastPage());
    }

    private function createMockExpense(string $type, float $cost, string $date)
    {
        return (object)[
            'type' => $type,
            'cost' => $cost,
            'created_at' => $date,
            'vehicle' => (object)[
                'id' => 1,
                'name' => 'Test Vehicle',
                'plate_number' => 'ABC-123',
            ],
        ];
    }

    private function createMockDTO(
        int $id,
        string $name,
        string $plate,
        string $type,
        float $cost,
        string $date
    ): VehicleExpenseDTO {
        return new VehicleExpenseDTO(
            id: $id,
            vehicleName: $name,
            plateNumber: $plate,
            type: $type,
            cost: $cost,
            createdAt: $date
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
