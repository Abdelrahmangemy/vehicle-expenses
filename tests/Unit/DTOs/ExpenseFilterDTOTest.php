<?php

namespace Tests\Unit\DTOs;

use Tests\TestCase;
use App\DTOs\ExpenseFilterDTO;
use Illuminate\Http\Request;

class ExpenseFilterDTOTest extends TestCase
{
    public function test_from_request_creates_dto_with_all_parameters()
    {
        $request = Request::create('/test', 'GET', [
            'search' => 'Rey Murray',
            'types' => 'fuel,insurance',
            'min_cost' => '100',
            'max_cost' => '500',
            'min_date' => '2025-01-01',
            'max_date' => '2025-12-31',
            'sort_by' => 'cost',
            'sort_direction' => 'asc',
        ]);

        $dto = ExpenseFilterDTO::fromRequest($request);

        $this->assertEquals('Rey Murray', $dto->search);
        $this->assertEquals(['fuel', 'insurance'], $dto->types);
        $this->assertEquals(100.0, $dto->minCost);
        $this->assertEquals(500.0, $dto->maxCost);
        $this->assertEquals('2025-01-01', $dto->minDate);
        $this->assertEquals('2025-12-31', $dto->maxDate);
        $this->assertEquals('cost', $dto->sortBy);
        $this->assertEquals('asc', $dto->sortDirection);
    }
}
