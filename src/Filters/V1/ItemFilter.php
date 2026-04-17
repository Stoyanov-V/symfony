<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

final class ItemFilter extends ApiFilter
{
    protected array $filterable = [
        'name' => ['eq'],
        'price' => ['eq', 'neq', 'lt', 'lte', 'gt', 'gte'],
        'category' => ['eq'],
        'restaurant' => ['eq'],
    ];

    protected array $sortable = ['name', 'price'];

    protected array $context = [
        'default' => 'item:read',
        'restaurant' => 'item:read:with-restaurant',
        'categories' => 'item:read:with-categories',
    ];
}
