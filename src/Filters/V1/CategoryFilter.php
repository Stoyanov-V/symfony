<?php

declare(strict_types=1);

namespace App\Filters\V1;

use App\Filters\ApiFilter;

final class CategoryFilter extends ApiFilter
{
    protected array $filterable = [
        'name' => ['eq'],
    ];

    protected array $sortable = ['name'];

    protected array $context = [
        'default' => 'category:read',
        'restaurant' => 'category:read:with-restaurant',
    ];
}
