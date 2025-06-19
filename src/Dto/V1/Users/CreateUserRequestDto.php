<?php

declare(strict_types=1);

namespace App\Dto\V1\Users;

final readonly class CreateUserRequestDto
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    )
    {
    }
}
