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

    protected array $sortable = ['id', 'name'];
}
