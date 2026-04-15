<?php

declare(strict_types=1);

namespace App\Service\Mappers;

use App\Dto\V1\Users\CreateUserRequestDto;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final readonly class UserMapper extends EntityMapper
{
    public function __construct(
        DenormalizerInterface $denormalizer,
        private UserPasswordHasherInterface $passwordHasher,
    )
    {
        parent::__construct($denormalizer);
    }
    protected function handleAssociations(object $dto, object $entity): void
    {
        if (!$entity instanceof User) {
            return;
        }

        if ($dto instanceof CreateUserRequestDto) {
            $entity->password = $this->passwordHasher->hashPassword(
                $entity,
                $dto->password
            );
        }
    }
}
