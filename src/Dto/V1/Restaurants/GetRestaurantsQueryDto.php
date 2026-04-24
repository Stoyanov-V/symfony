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
        public ?string $sort = null,
        public ?string $include = null,
        public ?string $q = null,
    ) {
        parent::__construct($sort, $include, $q);
    }
}
