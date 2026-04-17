<?php

declare(strict_types=1);

namespace App\Dto\V1\Restaurants;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateRestaurantRequestDto
{
    /**
     * @param array<string>|null $userIds
     */
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 2, max: 255)]
        public string $name,

        #[Assert\All([new Assert\Uuid()])]
        public ?array $userIds = null,
    ) {}
}
