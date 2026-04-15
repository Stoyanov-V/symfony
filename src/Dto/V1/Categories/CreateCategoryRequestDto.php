<?php

declare(strict_types=1);

namespace App\Dto\V1\Categories;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateCategoryRequestDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 2, max: 255)]
        public string $name,

        #[Assert\NotBlank]
        public Uuid $restaurantId,
    ) {
    }
}
