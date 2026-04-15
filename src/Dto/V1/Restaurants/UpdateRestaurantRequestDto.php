<?php

declare(strict_types=1);

namespace App\Dto\V1\Restaurants;

use App\Dto\V1\EntityMappableInterface;
use App\Entity\Restaurant;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateRestaurantRequestDto implements EntityMappableInterface
{
    public function __construct(
        #[Assert\NotBlank(groups: ['put', 'patch'])]
        #[Assert\Length(min: 2, max: 255, groups: ['put', 'patch'])]
        public ?string $name = null,

        /** @var array<string>|null */
        #[Assert\All([new Assert\Uuid()])]
        public ?array $userIds = null,
    ){}

    public static function fromEntity(object $entity): self
    {
        assert($entity instanceof Restaurant);

        return new self(
            name: $entity->name,
            userIds: $entity->users
                ->map(fn($user) => $user->id?->toRfc4122())
                ->filter(fn(?string $id) => $id !== null)
                ->toArray()
        );
    }
}
