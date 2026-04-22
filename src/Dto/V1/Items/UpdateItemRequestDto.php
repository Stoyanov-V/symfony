<?php

declare(strict_types=1);

namespace App\Dto\V1\Items;

use App\Dto\V1\EntityMappableInterface;
use App\Entity\Item;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateItemRequestDto implements EntityMappableInterface
{
    /**
     * @param  array<string, string>|null  $name
     * @param  array<string, string>|null  $description
     * @param array<string>|null $categoryIds
     */
    public function __construct(
        #[Assert\NotBlank(groups: ['item:put'])]
        #[Assert\Type('array', groups: ['item:put', 'item:patch'])]
        #[Assert\Count(min: 1, groups: ['item:put', 'item:patch'])]
        #[Assert\All([
            new Assert\Type('string'),
            new Assert\Length(min: 2, max: 255)
        ], groups: ['item:put', 'item:patch'])]
        public ?array $name = null,

        #[Assert\NotBlank(groups: ['item:put'])]
        #[Assert\PositiveOrZero(groups: ['item:put', 'item:patch'])]
        public ?string $price = null,

        #[Assert\NotBlank(groups: ['item:put'])]
        public ?Uuid $restaurantId = null,

        #[Assert\Type('array', groups: ['item:put', 'item:patch'])]
        #[Assert\All([
            new Assert\Type('string'),
            new Assert\Length(max: 2000)
        ], groups: ['item:put', 'item:patch'])]
        public ?array $description = null,

        #[Assert\All([new Assert\Uuid()], groups: ['item:put', 'item:patch'])]
        public ?array $categoryIds = null,
    ) {}

    public static function fromEntity(object $entity): self
    {
        assert($entity instanceof Item);

        return new self(
            name: $entity->name,
            price: $entity->price,
            restaurantId: $entity->restaurant?->id,
            description: $entity->description,
            categoryIds: $entity->categories->map(fn($c) => $c->id?->toRfc4122())->toArray()
        );
    }
}
