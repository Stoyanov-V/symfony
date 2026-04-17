<?php

declare(strict_types=1);

namespace App\Service\Mappers;

use App\Entity\{Category, Item, Restaurant};
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use RuntimeException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Uid\Uuid;

final readonly class ItemMapper extends EntityMapper{
    public function __construct(
        DenormalizerInterface $denormalizer,
        private EntityManagerInterface $em,
    ) {
        parent::__construct($denormalizer);
    }

    protected function handleAssociations(object $dto, object $entity): void
    {
        if (!$entity instanceof Item) {
            return;
        }

        if (isset($dto->restaurantId)) {
            try {
                $entity->restaurant = $this->em->getReference(Restaurant::class, $dto->restaurantId);
            } catch (ORMException $e) {
                throw new RuntimeException("Failed to get Restaurant reference: " . $e->getMessage(), 0, $e);
            }
        }

        if (isset($dto->categoryIds)) {
            $this->syncCategories($entity, $dto->categoryIds);
        }
    }

    /**
     * @param  array<string>  $dtoCategoryIds
     */
    private function syncCategories(Item $item, array $dtoCategoryIds): void
    {
        foreach ($item->categories as $category) {
            if (!in_array($category->id?->toRfc4122(), $dtoCategoryIds, true)) {
                $item->removeCategory($category);
            }
        }

        foreach ($dtoCategoryIds as $categoryId) {
            try {
                $uuid = Uuid::fromString($categoryId);
                $item->addCategory($this->em->getReference(Category::class, $uuid));
            } catch (ORMException $e) {
                throw new RuntimeException("Failed to get Category reference: " . $e->getMessage(), 0, $e);
            }
        }
    }
}
