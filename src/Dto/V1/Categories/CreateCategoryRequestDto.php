<?php

declare(strict_types=1);

namespace App\Dto\V1\Categories;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateCategoryRequestDto
{
    /**
     * @param array<string, string> $name
     */
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('array')]
        #[Assert\Count(min: 1)]
        #[Assert\All([
            new Assert\Type('string'),
            new Assert\Length(min: 2, max: 255)
        ])]
        public array $name,

        #[Assert\NotBlank]
        public Uuid $restaurantId,
    ) {
    }
}
