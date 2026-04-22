<?php

declare(strict_types=1);

namespace App\Dto\V1\Categories;

use App\Dto\V1\EntityMappableInterface;
use App\Entity\Category;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateCategoryRequestDto implements EntityMappableInterface
{
    /**
     * @param array<string, string>|null $name
     */
    public function __construct(
        #[Assert\NotBlank(groups: ['put', 'patch'])]
        #[Assert\Type('array', groups: ['put', 'patch'])]
        #[Assert\Count(min: 1, groups: ['put', 'patch'])]
        #[Assert\All([
            new Assert\Type('string'),
            new Assert\Length(min: 2, max: 255)
        ], groups: ['put', 'patch'])]
        public ?array $name = null,

        #[Assert\NotBlank(groups: ['put'])]
        public ?Uuid $restaurantId = null,
    ){}

    public static function fromEntity(object $entity): EntityMappableInterface
    {
        assert($entity instanceof Category);

        return new self(
            name: $entity->name,
            restaurantId: $entity->restaurant?->id
        );
    }
}
