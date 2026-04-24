<?php

declare(strict_types=1);

namespace App\Filters\V1;

use App\Filters\ApiFilter;

final class RestaurantFilter extends ApiFilter
{
    protected array $filterable = [
        'name' => ['eq'],
    ];

    protected array $sortable = ['name'];

    protected array $searchable = ['name'];

    protected array $context = [
        'default' => 'restaurant:read',
        'users' => 'restaurant:read:with-users',
        'categories' => 'restaurant:read:with-categories',
        'items' => 'restaurant:read:with-items',
    ];
}
