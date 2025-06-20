<?php

declare(strict_types=1);

namespace App\Dto\V1\Users;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateUserRequestDto
{
    /**
     * @param  string  $name
     * @param  string  $email
     * @param  string  $password
     * @param  array<string>  $roles
     */
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 255)]
        public string $name,
        #[Assert\NotBlank]
        #[Assert\Length(max: 180)]
        #[Assert\Email]
        public string $email,
        #[Assert\NotBlank]
        #[Assert\Length(min: 6)]
        #[Assert\PasswordStrength(
            minScore: Assert\PasswordStrength::STRENGTH_MEDIUM,
        )]
        public string $password,
        #[Assert\Type('array')]
        #[Assert\All([
            new Assert\NotBlank(),
            new Assert\Type('string'),
            new Assert\Choice(choices: ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_MANAGER']),
        ])]
        public array $roles = ['ROLE_USER'],
    )
    {
    }
}
