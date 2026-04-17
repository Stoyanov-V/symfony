<?php

declare(strict_types=1);

namespace App\Service\Mappers;

use App\Entity\User;
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

        if (isset($dto->userIds)) {
            $this->syncUsers($entity, $dto->userIds);
        }
    }

    /** @param array<string> $dtoUserIds */
    private function syncUsers(Restaurant $restaurant, array $dtoUserIds): void
    {
        foreach ($restaurant->users as $user) {
            $userIdString = $user->id?->toRfc4122();
            if ($userIdString && !in_array($userIdString, $dtoUserIds, true)) {
                $restaurant->removeUser($user);
            }
        }

        foreach ($dtoUserIds as $userId) {
            try {
                $uuid = Uuid::fromString($userId);
                $restaurant->addUser($this->em->getReference(User::class, $uuid));
            } catch (ORMException $e) {
                throw new RuntimeException("Failed to get User reference: " . $e->getMessage(), 0, $e);
            }
        }
    }
}
