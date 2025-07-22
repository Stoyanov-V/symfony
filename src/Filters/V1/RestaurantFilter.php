<?php

declare(strict_types=1);

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class RestaurantFilter extends ApiFilter
{
    protected array $filterable = [
        'name' => ['eq'],
    ];

    protected array $sortable = ['id', 'name'];

    protected array $context = [
        'default' => 'restaurant:read',
        'users' => 'restaurant:read:with-users',
        'categories' => 'restaurant:read:with-categories',
        'items' => 'restaurant:read:with-items',
    ];
}
