<?php

declare(strict_types=1);

namespace App\Dto\V1\Categories;

use App\Dto\V1\GetQueryDto;

final class GetCategoryQueryDto extends GetQueryDto
{
    /**
     * @param  array<string, string>|null  $name
     * @param  string|null  $sort
     * @param  string|null  $include
     * @param  string|null  $q
     */
    public function __construct(
        public ?array $name = null,
        public ?string $sort = null,
        public ?string $include = null,
        public ?string $q = null,
    )
    {
        parent::__construct($sort, $include, $q);
    }
}
