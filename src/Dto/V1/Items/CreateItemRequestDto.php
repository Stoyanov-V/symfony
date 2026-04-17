<?php

declare(strict_types=1);

namespace App\Dto\V1\Items;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
final readonly class CreateItemRequestDto
{
    /**
     * @param array<string>|null $categoryIds
     */
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 2, max: 255)]
        public string $name,

        #[Assert\NotBlank]
        #[Assert\PositiveOrZero]
        public string $price,

        #[Assert\NotBlank]
        public Uuid $restaurantId,

        #[Assert\Length(max: 2000)]
        public ?string $description = null,

        #[Assert\All([new Assert\Uuid()])]
        public ?array $categoryIds = null,
    ) {}
}
