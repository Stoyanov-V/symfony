<?php

declare(strict_types=1);

namespace App\Dto\V1\Auth;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class LoginRequestDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,

        #[Assert\NotBlank]
        public string $password,
    ) {}
}
