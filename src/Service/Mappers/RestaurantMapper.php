<?php

declare(strict_types=1);

namespace App\Service\Mappers;

use App\Entity\User;
use App\Dto\V1\Restaurants\{CreateRestaurantRequestDto, UpdateRestaurantRequestDto};
use App\Entity\Restaurant;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use RuntimeException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Uid\Uuid;

final readonly class RestaurantMapper extends EntityMapper
{
    public function __construct(
        DenormalizerInterface $denormalizer,
        private EntityManagerInterface $em,
    )
    {
        parent::__construct($denormalizer);
    }

    protected function handleAssociations(object $dto, object $entity): void
    {
        if (!$entity instanceof Restaurant) {
            return;
        }

        if (!$dto instanceof CreateRestaurantRequestDto && !$dto instanceof UpdateRestaurantRequestDto) {
            return;
        }

        if ($dto->userIds === null) {
            return;
        }

        foreach ($entity->users as $user) {
            $userIdString = $user->id?->toRfc4122();
            if ($userIdString && !in_array($userIdString, $dto->userIds, true)) {
                $entity->removeUser($user);
            }
        }

        foreach ($dto->userIds as $userId) {
            $uuid = $userId instanceof Uuid ? $userId : Uuid::fromString($userId);

            try {
                $userReference = $this->em->getReference(User::class, $uuid);
            } catch (ORMException $e) {
                throw new RuntimeException("Failed to create Restaurant reference: " . $e->getMessage());
            }

            $entity->addUser($userReference);
        }

    }
}
