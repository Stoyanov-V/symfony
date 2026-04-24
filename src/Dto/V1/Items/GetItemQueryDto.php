<?php

namespace App\Dto\V1\Items;

use App\Dto\V1\GetQueryDto;

final class GetItemQueryDto extends GetQueryDto
{
    /**
     * @param  array<string, string>|null  $name
     * @param  array<string, string>|null  $price
     * @param  array<string, string>|null  $category
     * @param  array<string, string>|null  $restaurant
     * @param  string|null  $sort
     * @param  string|null  $include
     * @param  string|null  $q
     */
    public function __construct(
        public ?array $name = null,
        public ?array $price = null,
        public ?array $category = null,
        public ?array $restaurant = null,
        public ?string $sort = null,
        public ?string $include = null,
        public ?string $q = null,
    ) {
        parent::__construct($sort, $include, $q);
    }
}
