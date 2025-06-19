<?php

declare(strict_types=1);

namespace App\Dto\V1\Users;

use App\Dto\V1\GetQueryDto;

final class GetUserQueryDto extends GetQueryDto
{
    /**
     * @param  array<string, string>|null  $name
     * @param  array<string, string>|null  $email
     * @param  string|null  $sort
     * @param  string|null  $include
     */
    public function __construct(
        public ?array $name = null,
        public ?array $email = null,
        public ?string $sort = null,
        public ?string $include = null,
    )
    {
        parent::__construct($sort, $include);
    }
}
