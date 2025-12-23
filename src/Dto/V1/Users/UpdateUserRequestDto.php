<?php

declare(strict_types=1);

namespace App\Dto\V1\Users;

use App\Dto\V1\EntityMappableInterface;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateUserRequestDto implements EntityMappableInterface
{
    /**
     * @param  ?string  $name
     * @param  ?string  $email
     * @param  ?array<string>  $roles
     */
    public function __construct(
        #[Assert\NotBlank(groups: ['put', 'patch'])]
        #[Assert\Length(min: 3, max: 255, groups: ['put', 'patch'])]
        public ?string $name = null,

        #[Assert\NotBlank(groups: ['put', 'patch'])]
        #[Assert\Length(max: 180, groups: ['put', 'patch'])]
        #[Assert\Email(groups: ['put', 'patch'])]
        public ?string $email = null,

        #[Assert\Type('array')]
        #[Assert\NotBlank(groups: ['put', 'patch'])]
        #[Assert\All([
            new Assert\Type('string', groups: ['put', 'patch']),
            new Assert\Choice(
                choices: ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_MANAGER'],
                groups: ['put', 'patch']
            ),
        ])]
        public ?array $roles = null,
    ) {}

    /** @noinspection PhpUnused */
    public static function fromEntity(object $entity): self
    {
        assert($entity instanceof User);

        return new self(
            name: $entity->name,
            email: $entity->email,
            roles: $entity->getRoles(),
        );
    }
}
