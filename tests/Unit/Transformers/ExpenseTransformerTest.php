<?php

namespace Tests\Unit\Transformers;

use Tests\TestCase;
use App\Transformers\ExpenseTransformer;
use App\Models\Vehicle;
use App\DTOs\VehicleExpenseDTO;

class ExpenseTransformerTest extends TestCase
{
    private ExpenseTransformer $transformer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transformer = new ExpenseTransformer();
    }

    public function test_it_transforms_fuel_expense_correctly()
    {
        $vehicle = new Vehicle();
        $vehicle->id = 1;
        $vehicle->name = 'Rey Murray';
        $vehicle->plate_number = 'ABC-123';
        $vehicle->exists = true;

        $expense = (object)[
            'vehicle' => $vehicle,
            'cost' => 150.50,
            'created_at' => '2025-01-15 10:30:00',
            'type' => 'fuel',
        ];

        $dto = $this->transformer->transform($expense, 'fuel');

        $this->assertInstanceOf(VehicleExpenseDTO::class, $dto);

        $this->assertEquals(1, $dto->id);
        $this->assertEquals('Rey Murray', $dto->vehicleName);
        $this->assertEquals('ABC-123', $dto->plateNumber);
        $this->assertEquals('fuel', $dto->type);
        $this->assertEquals(150.50, $dto->cost);
        $this->assertEquals('2025-01-15 10:30:00', $dto->createdAt);
    }

    public function test_it_transforms_insurance_expense_correctly()
    {
        $vehicle = new Vehicle();
        $vehicle->id = 2;
        $vehicle->name = 'Honda Civic';
        $vehicle->plate_number = 'XYZ-789';
        $vehicle->exists = true;

        $expense = (object)[
            'vehicle' => $vehicle,
            'cost' => 2000.00,
            'created_at' => '2025-02-01',
            'type' => 'insurance',
        ];

        $dto = $this->transformer->transform($expense, 'insurance');

        $this->assertInstanceOf(VehicleExpenseDTO::class, $dto);
        $this->assertEquals(2, $dto->id);
        $this->assertEquals('Honda Civic', $dto->vehicleName);
        $this->assertEquals('insurance', $dto->type);
        $this->assertEquals(2000.00, $dto->cost);
        $this->assertEquals('2025-02-01', $dto->createdAt);
    }

    public function test_it_transforms_service_expense_correctly()
    {
        $vehicle = new Vehicle();
        $vehicle->id = 3;
        $vehicle->name = 'Toyota Hilux';
        $vehicle->plate_number = 'LMN-456';
        $vehicle->exists = true;

        $expense = (object)[
            'vehicle' => $vehicle,
            'cost' => 800.00,
            'created_at' => '2025-03-10',
            'type' => 'service',
        ];

        $dto = $this->transformer->transform($expense, 'service');

        $this->assertInstanceOf(VehicleExpenseDTO::class, $dto);
        $this->assertEquals(3, $dto->id);
        $this->assertEquals('Toyota Hilux', $dto->vehicleName);
        $this->assertEquals('service', $dto->type);
        $this->assertEquals(800.00, $dto->cost);
        $this->assertEquals('2025-03-10', $dto->createdAt);
    }

    public function test_dto_to_array_returns_correct_structure()
    {
        $vehicle = new Vehicle();
        $vehicle->id = 4;
        $vehicle->name = 'BMW X5';
        $vehicle->plate_number = 'DEF-222';
        $vehicle->exists = true;

        $expense = (object)[
            'vehicle' => $vehicle,
            'cost' => 2500.75,
            'created_at' => '2025-04-05',
            'type' => 'fuel',
        ];

        $dto = $this->transformer->transform($expense, 'fuel');

        $array = $dto->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('vehicle_name', $array);
        $this->assertArrayHasKey('plate_number', $array);
        $this->assertArrayHasKey('type', $array);
        $this->assertArrayHasKey('cost', $array);
        $this->assertArrayHasKey('created_at', $array);

        $this->assertEquals(4, $array['id']);
        $this->assertEquals('BMW X5', $array['vehicle_name']);
        $this->assertEquals('DEF-222', $array['plate_number']);
        $this->assertEquals('fuel', $array['type']);
        $this->assertEquals(2500.75, $array['cost']);
        $this->assertEquals('2025-04-05', $array['created_at']);
    }
}
