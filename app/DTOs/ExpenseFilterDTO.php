<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class ExpenseFilterDTO
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?array $types = null,
        public readonly ?float $minCost = null,
        public readonly ?float $maxCost = null,
        public readonly ?string $minDate = null,
        public readonly ?string $maxDate = null,
        public readonly string $sortBy = 'created_at',
        public readonly string $sortDirection = 'desc',
        public readonly int $perPage = 10,
        public readonly int $page = 1

    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            search: $request->input('search'),
            types: $request->input('types') ?
                (is_array($request->input('types')) ?
                    $request->input('types') :
                    explode(',', $request->input('types'))) :
                null,
            minCost: $request->input('min_cost') ?
                (float) $request->input('min_cost') :
                null,
            maxCost: $request->input('max_cost') ?
                (float) $request->input('max_cost') :
                null,
            minDate: $request->input('min_date'),
            maxDate: $request->input('max_date'),
            sortBy: $request->input('sort_by', 'created_at'),
            sortDirection: $request->input('sort_direction', 'desc'),
            perPage: (int) $request->input('per_page', 10),
            page: (int) $request->input('page', 1)
        );
    }
}
