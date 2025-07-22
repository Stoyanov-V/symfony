<?php

declare(strict_types=1);

namespace App\Dto\V1\Restaurants;

use App\Dto\V1\GetQueryDto;

final class GetRestaurantsQueryDto extends GetQueryDto
{
    /**
     * @param  array<string, string>|null  $name
     */
    public function __construct(
        public ?array $name = null,
        ?string $sort = null,
        ?string $include = null,
    )
    {
        parent::__construct($sort, $include);
    }
}
