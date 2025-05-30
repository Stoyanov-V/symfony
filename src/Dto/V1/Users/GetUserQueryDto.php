<?php

declare(strict_types=1);

namespace App\Dto\V1\Users;

use App\Dto\V1\GetQueryDto;

final class GetUserQueryDto extends GetQueryDto
{
    public function __construct(
        public ?array $name = null,
        public ?array $email = null,
        public ?string $sort = null,
    )
    {
        parent::__construct($sort);
    }
}
