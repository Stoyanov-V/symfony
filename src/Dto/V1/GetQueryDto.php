<?php

declare(strict_types=1);

namespace App\Dto\V1;

class GetQueryDto
{
    public function __construct(
        public ?string $sort = null,
        public ?string $include = null,
        public int $perPage = 25,
        public int $page = 1,
    ) {
    }
}
