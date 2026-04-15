<?php

declare(strict_types=1);

namespace App\Service\Mappers;

use App\Dto\V1\Categories\{CreateCategoryRequestDto, UpdateCategoryRequestDto};
use App\Entity\{Category, Restaurant};
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use RuntimeException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final readonly class CategoryMapper extends EntityMapper
{
    public function __construct(
        DenormalizerInterface $denormalizer,
        private EntityManagerInterface $em,
    ) {
        parent::__construct($denormalizer);
    }

    protected function handleAssociations(object $dto, object $entity): void
    {
        if (!$entity instanceof Category) {
            return;
        }

        if (!$dto instanceof CreateCategoryRequestDto && !$dto instanceof UpdateCategoryRequestDto) {
            return;
        }
        if ($dto->restaurantId === null) {
            return;
        }

        try {
            $entity->restaurant = $this->em->getReference(Restaurant::class, $dto->restaurantId);
        } catch (ORMException $e) {
            throw new RuntimeException("Failed to create Restaurant reference: " . $e->getMessage());
        }
    }
}
