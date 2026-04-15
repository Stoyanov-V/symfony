<?php

declare(strict_types=1);

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class UserFilter extends ApiFilter
{
    protected array $filterable = [
        'name' => ['eq'],
        'email' => ['eq'],
    ];

    protected array $sortable = ['name'];

    protected array $context = [
        'default' => 'user:read',
        'restaurants' => 'user:read:with-restaurants',
    ];
}
